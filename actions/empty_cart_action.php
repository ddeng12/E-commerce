<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to manage your cart']);
    exit;
}

require_once '../controllers/cart_controller.php';

$controller = new CartController();
$result = $controller->clear_cart_ctr($_SESSION['user_id']);
echo json_encode($result);
?>

