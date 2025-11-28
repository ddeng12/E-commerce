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

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['image'];
$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 'new';

// If product_id is 'new', generate a temporary ID for folder structure
if ($product_id === 'new') {
    $product_id = 'temp_' . time();
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed']);
    exit;
}

// Validate file size (5MB max)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed']);
    exit;
}

// Create upload directory structure
// Get absolute path to uploads directory
$base_uploads_dir = dirname(__DIR__) . '/uploads/';

// Create user directory if it doesn't exist
$user_dir = $base_uploads_dir . 'u' . $user_id . '/';
if (!is_dir($user_dir)) {
    $result = @mkdir($user_dir, 0777, true);
    if (!$result) {
        $error = error_get_last();
        echo json_encode(['success' => false, 'message' => 'Failed to create user directory', 'error' => $error['message']]);
        exit;
    }
}

// Create product directory
$upload_dir = $user_dir . 'p' . $product_id . '/';
if (!is_dir($upload_dir)) {
    $result = @mkdir($upload_dir, 0777, true);
    if (!$result) {
        $error = error_get_last();
        echo json_encode(['success' => false, 'message' => 'Failed to create product directory', 'error' => $error['message'], 'path' => $upload_dir]);
        exit;
    }
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'image_' . time() . '_' . uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Return relative path from web root
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
    echo json_encode(['success' => true, 'message' => 'Image uploaded successfully', 'image_path' => $relative_path]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
}
?>
