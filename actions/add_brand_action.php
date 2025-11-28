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

require_once '../controllers/brand_controller.php';

$controller = new BrandController();

$args = [
    'name' => $_POST['name'] ?? '',
    'category_id' => $_POST['category_id'] ?? '',
    'created_by' => $_SESSION['user_id']
];

$result = $controller->add_brand_ctr($args);
echo json_encode($result);
?>
