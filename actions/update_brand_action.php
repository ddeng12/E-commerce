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

$id = $_POST['id'] ?? '';
$args = [
    'name' => $_POST['name'] ?? '',
    'category_id' => $_POST['category_id'] ?? ''
];

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Brand ID is required']);
    exit;
}

$result = $controller->edit_brand_ctr($id, $args, $_SESSION['user_id']);
echo json_encode($result);
?>
