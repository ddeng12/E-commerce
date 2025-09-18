<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once 'customer_controller.php';

$controller = new CustomerController();

$args = [
    'full_name' => $_POST['full_name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'password' => $_POST['password'] ?? '',
    'country' => $_POST['country'] ?? '',
    'city' => $_POST['city'] ?? '',
    'contact_number' => $_POST['contact_number'] ?? '',
    'user_role' => $_POST['user_role'] ?? 2,
    'image' => $_POST['image'] ?? null
];

$result = $controller->register_customer_ctr($args);
echo json_encode($result);
?>