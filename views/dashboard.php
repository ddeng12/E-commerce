<?php
require_once '../core.php';

// Require user to be logged in to access this page
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>
    <h1>User Dashboard</h1>
    
    <nav>
        <a href="index.php">← Back to Home</a> | 
        <a href="logout.php">Logout</a>
    </nav>
    
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    
    <p><strong>User Role:</strong> <?php echo getUserRoleName(); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    
    <?php if (isAdmin()): ?>
        <p><a href="admin.php">Go to Admin Panel</a></p>
    <?php endif; ?>
    
    <h3>Your Account</h3>
    <ul>
        <li>View Profile</li>
        <li>Edit Settings</li>
        <li>Order History</li>
        <li>Change Password</li>
    </ul>
    
    <h3>Session Status</h3>
    <p><strong>Logged In:</strong> <?php echo isLoggedIn() ? 'Yes' : 'No'; ?></p>
    <p><strong>Admin Access:</strong> <?php echo isAdmin() ? 'Yes' : 'No'; ?></p>
</body>
</html>
