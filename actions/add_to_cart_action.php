<?php
session_start();
header('Content-Type: application/json');

// Debug logging
error_log('Add to cart action called');
error_log('POST data: ' . print_r($_POST, true));
error_log('Session data: ' . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
    exit;
}

require_once '../controllers/cart_controller.php';

$product_id = $_POST['product_id'] ?? '';
$quantity = (int)($_POST['quantity'] ?? 1);

error_log("Product ID: $product_id, Quantity: $quantity, User ID: " . $_SESSION['user_id']);

if (empty($product_id)) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    $controller = new CartController();
    $result = $controller->add_to_cart_ctr($_SESSION['user_id'], $product_id, $quantity);
    error_log('Result: ' . print_r($result, true));
    echo json_encode($result);
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

