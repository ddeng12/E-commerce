<?php
require_once '../core.php';

// Require admin privileges to access this page
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - 45G1 Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 40px;
            border-radius: 24px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(220, 53, 69, 0.3);
        }
        
        .admin-header h1 {
            font-size: 3em;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .admin-header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .admin-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-nav a {
            color: #dc3545;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            margin-right: 10px;
            display: inline-block;
        }
        
        .admin-nav a:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .admin-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-section h2 {
            color: #dc3545;
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(220, 53, 69, 0.2);
        }
        
        .admin-section h3 {
            color: #2d3748;
            font-size: 1.4em;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .admin-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border: 1px solid rgba(220, 53, 69, 0.1);
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .admin-card h4 {
            color: #dc3545;
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .admin-card ul {
            list-style: none;
            padding: 0;
        }
        
        .admin-card li {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }
        
        .admin-card li:last-child {
            border-bottom: none;
        }
        
        .user-info {
            background: rgba(220, 53, 69, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .user-info p {
            margin: 8px 0;
            color: #2d3748;
        }
        
        .user-info strong {
            color: #dc3545;
        }
        
        .session-info {
            background: rgba(108, 117, 125, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .session-info pre {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            overflow-x: auto;
        }
        
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-nav a {
                display: block;
                margin-bottom: 10px;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Admin Panel</h1>
            <p>Welcome to the E-commerce Administration Dashboard</p>
        </div>
        
        <div class="admin-nav">
            <a href="product.php">Product Management</a>
            <a href="category.php">Category Management</a>
            <a href="brand.php">Brand Management</a>
            <a href="../assets/logout.php">Logout</a>
        </div>
        
        <div class="admin-section">
            <h2>Admin Functions</h2>
            <div class="admin-grid">
                <div class="admin-card">
                    <h4>Product Management</h4>
                    <ul>
                        <li>Add new products</li>
                        <li>Edit existing products</li>
                        <li>Delete products</li>
                        <li>Upload product images</li>
                        <li>Manage product details</li>
                    </ul>
                    <a href="product.php" class="btn-admin">Manage Products</a>
                </div>
                
                <div class="admin-card">
                    <h4>Category Management</h4>
                    <ul>
                        <li>Create categories</li>
                        <li>Edit categories</li>
                        <li>Delete categories</li>
                        <li>Organize product categories</li>
                    </ul>
                    <a href="category.php" class="btn-admin">Manage Categories</a>
                </div>
                
                <div class="admin-card">
                    <h4>Brand Management</h4>
                    <ul>
                        <li>Add new brands</li>
                        <li>Edit brand information</li>
                        <li>Delete brands</li>
                        <li>Link brands to categories</li>
                    </ul>
                    <a href="brand.php" class="btn-admin">Manage Brands</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
