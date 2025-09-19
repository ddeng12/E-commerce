<?php
session_start();
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
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span> | 
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a> | 
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</body>
</html>
