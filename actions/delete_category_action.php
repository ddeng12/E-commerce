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

require_once '../controllers/category_controller.php';

$controller = new CategoryController();

$id = $_POST['id'] ?? '';

$result = $controller->delete_category_ctr($id, $_SESSION['user_id']);
echo json_encode($result);
?>
