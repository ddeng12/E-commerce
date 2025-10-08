<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../controllers/category_controller.php';

$controller = new CategoryController();
$categories = $controller->get_categories_ctr($_SESSION['user_id']);

if ($categories !== false) {
    echo json_encode(['success' => true, 'data' => $categories]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch categories']);
}
?>
