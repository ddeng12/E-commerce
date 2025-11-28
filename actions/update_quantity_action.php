<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your cart']);
    exit;
}

require_once '../controllers/cart_controller.php';

$cart_item_id = $_POST['cart_item_id'] ?? '';
$quantity = (int)($_POST['quantity'] ?? 1);

if (empty($cart_item_id)) {
    echo json_encode(['success' => false, 'message' => 'Cart item ID is required']);
    exit;
}

if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

$controller = new CartController();
$result = $controller->update_quantity_ctr($cart_item_id, $_SESSION['user_id'], $quantity);
echo json_encode($result);
?>

