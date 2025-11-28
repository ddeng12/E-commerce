<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to checkout']);
    exit;
}

require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/product_controller.php';

$customer_id = $_SESSION['user_id'];

try {
    // Step 1: Get cart items
    $cart_controller = new CartController();
    $cart_items = $cart_controller->get_cart_items_ctr($customer_id);
    
    if (empty($cart_items)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit;
    }
    
    // Step 2: Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Step 3: Generate order reference
    $order_controller = new OrderController();
    $order_reference = $order_controller->generate_order_reference_ctr();
    
    // Step 4: Create order
    $order_params = [
        'customer_id' => $customer_id,
        'order_reference' => $order_reference,
        'total_amount' => $total_amount,
        'status' => 'pending'
    ];
    
    $order_result = $order_controller->create_order_ctr($order_params);
    
    if (!$order_result['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $order_result['message']]);
        exit;
    }
    
    $order_id = $order_result['order_id'];
    
    // Step 5: Add order details
    $all_details_success = true;
    $details_errors = [];
    
    foreach ($cart_items as $item) {
        $detail_params = [
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'subtotal' => $item['price'] * $item['quantity']
        ];
        
        $detail_result = $order_controller->add_order_details_ctr($detail_params);
        
        if (!$detail_result['success']) {
            $all_details_success = false;
            $details_errors[] = $detail_result['message'];
        }
    }
    
    if (!$all_details_success) {
        // Rollback: Delete the order if details failed
        // Note: In production, you'd want to use transactions
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to add order details: ' . implode(', ', $details_errors)
        ]);
        exit;
    }
    
    // Step 6: Record payment
    $transaction_reference = $order_controller->generate_transaction_reference_ctr();
    
    $payment_params = [
        'order_id' => $order_id,
        'payment_method' => 'simulated',
        'amount' => $total_amount,
        'payment_status' => 'completed',
        'transaction_reference' => $transaction_reference
    ];
    
    $payment_result = $order_controller->record_payment_ctr($payment_params);
    
    if (!$payment_result['success']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Order created but payment recording failed: ' . $payment_result['message']
        ]);
        exit;
    }
    
    // Step 7: Clear cart
    $clear_result = $cart_controller->clear_cart_ctr($customer_id);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order processed successfully',
        'order_id' => $order_id,
        'order_reference' => $order_reference,
        'transaction_reference' => $transaction_reference,
        'total_amount' => $total_amount,
        'cart_cleared' => $clear_result['success']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error processing checkout: ' . $e->getMessage()
    ]);
}
?>

