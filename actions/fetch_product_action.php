<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../controllers/product_controller.php';

$controller = new ProductController();
$products = $controller->get_products_ctr($_SESSION['user_id']);

if ($products !== false) {
    echo json_encode(['success' => true, 'data' => $products]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch products']);
}
?>
