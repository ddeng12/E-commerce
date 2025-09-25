<?php
require_once 'core.php';

// Require admin privileges to access this page
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Admin Panel</h1>
    
    <nav>
        <a href="index.php">← Back to Home</a> | 
        <a href="logout.php">Logout</a>
    </nav>
    
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    
    <p><strong>User Role:</strong> <?php echo getUserRoleName(); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    
    <h3>Admin Functions</h3>
    <ul>
        <li>Manage Users</li>
        <li>View System Logs</li>
        <li>Configure Settings</li>
        <li>Database Management</li>
    </ul>
    
    <h3>Session Information</h3>
    <pre><?php print_r(getCurrentUser()); ?></pre>
</body>
</html>
