<?php
require_once 'core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Platform</title>
</head>
<body>
    <h1>Welcome to the Platform</h1>
    
    <nav>
        <?php if (isLoggedIn()): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            | <a href="dashboard.php">Dashboard</a>
            <?php if (isAdmin()): ?>
                | <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a> | 
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</body>
</html>
