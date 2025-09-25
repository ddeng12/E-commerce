<?php
/**
 * Core functionality for session management and user privileges
 * Part 3: Session Management & Admin Privileges
 */

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is logged in by checking if a session has been created
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if a user has administrative privileges by checking the user's role
 * @return bool True if user has admin privileges (role = 1), false otherwise
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

/**
 * Get current user's information from session
 * @return array|false User data if logged in, false otherwise
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 2,
        'is_admin' => isAdmin()
    ];
}

/**
 * Require user to be logged in, redirect to login if not
 * @param string $redirect_url URL to redirect to after login (optional)
 */
function requireLogin($redirect_url = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Require admin privileges, redirect to index if not admin
 * @param string $redirect_url URL to redirect to if not admin (optional)
 */
function requireAdmin($redirect_url = 'index.php') {
    if (!isAdmin()) {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Check if user has specific role
 * @param int $role Role to check for (1 = admin, 2 = customer)
 * @return bool True if user has the specified role
 */
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role;
}

/**
 * Get user role name
 * @return string Role name (Admin, Customer, or Unknown)
 */
function getUserRoleName() {
    if (!isLoggedIn()) {
        return 'Unknown';
    }
    
    $role = $_SESSION['user_role'] ?? 2;
    return $role == 1 ? 'Admin' : 'Customer';
}
?>
