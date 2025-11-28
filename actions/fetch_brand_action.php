<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

require_once '../controllers/brand_controller.php';

$controller = new BrandController();
$brands = $controller->get_brands_ctr($_SESSION['user_id']);

if ($brands !== false) {
    echo json_encode(['success' => true, 'data' => $brands]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch brands']);
}
?>
