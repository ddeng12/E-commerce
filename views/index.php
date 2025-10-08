<?php
require_once '../core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Platform</title>
</head>
<body>
    <h1>Welcome David Deng</h1>
    
    <nav>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                   <a href="../assets/logout.php">Logout</a> | 
                   <a href="category.php">Category</a>
            <?php else: ?>
                <a href="../assets/logout.php">Logout</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="register.php">Register</a> | 
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</body>
</html>
