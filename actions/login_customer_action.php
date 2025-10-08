<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once '../controllers/customer_controller.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$controller = new CustomerController();
$result = $controller->login_customer_ctr($email, $password);

if ($result['success']) {
    $_SESSION['user_id'] = $result['user']['id'];
    $_SESSION['user_role'] = $result['user']['user_role'];
    $_SESSION['user_name'] = $result['user']['full_name'];
    $_SESSION['user_email'] = $result['user']['email'];
}

echo json_encode($result);
?>
