<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once '../controllers/product_controller.php';

$controller = new ProductController();

$args = [
    'title' => $_POST['title'] ?? '',
    'description' => $_POST['description'] ?? '',
    'price' => $_POST['price'] ?? '',
    'category_id' => $_POST['category_id'] ?? '',
    'brand_id' => $_POST['brand_id'] ?? '',
    'keyword' => $_POST['keyword'] ?? '',
    'image_path' => $_POST['image_path'] ?? null,
    'created_by' => $_SESSION['user_id']
];

$result = $controller->add_product_ctr($args);
echo json_encode($result);
?>
