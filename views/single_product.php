<?php
require_once '../core.php';
require_once '../actions/product_actions.php';

// Get product ID from URL
$product_id = $_GET['id'] ?? '';

if (empty($product_id)) {
    header('Location: all_product.php');
    exit;
}

// Get single product data
$productActions = new ProductActions();
$_GET['action'] = 'view_single';
$_GET['id'] = $product_id;
$result = $productActions->handleRequest();

if (!$result['success']) {
    header('Location: all_product.php');
    exit;
}

$product = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - E-commerce Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .nav a {
            color: #667eea;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .product-detail {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }
        
        .product-image-section {
            position: relative;
        }
        
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .product-id-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .product-info {
            padding: 20px 0;
        }
        
        .product-title {
            font-size: 2.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .product-price {
            font-size: 3em;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .product-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .meta-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .product-description {
            font-size: 1.1em;
            line-height: 1.6;
            color: #555;
            margin-bottom: 25px;
            background: rgba(102, 126, 234, 0.05);
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }
        
        .product-keywords {
            margin-bottom: 30px;
        }
        
        .keywords-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .keywords-list {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .keyword-tag {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: #764ba2;
            transform: translateX(-5px);
        }
        
        .product-stats {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .stats-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .stat-value {
            font-size: 1.5em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 25px;
            }
            
            .product-title {
                font-size: 2em;
            }
            
            .product-price {
                font-size: 2.5em;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .nav {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="all_product.php">All Products</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="product.php">Add Product</a>
                        <a href="brand.php">Brands</a>
                        <a href="category.php">Categories</a>
                    <?php endif; ?>
                    <a href="../assets/logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
        
        <a href="all_product.php" class="back-link">← Back to All Products</a>
        
        <div class="product-detail">
            <div class="product-image-section">
                <div class="product-id-badge">Product #<?php echo htmlspecialchars($product['id']); ?></div>
                <?php if ($product['image_path']): ?>
                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="product-image">
                <?php else: ?>
                    <div class="product-image"></div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h1>
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                
                <div class="product-meta">
                    <span class="meta-badge"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span class="meta-badge"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                </div>
                
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <strong>Description:</strong><br>
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($product['keyword'])): ?>
                    <div class="product-keywords">
                        <div class="keywords-label">Keywords:</div>
                        <div class="keywords-list">
                            <?php 
                            $keywords = explode(',', $product['keyword']);
                            foreach ($keywords as $keyword): 
                                $keyword = trim($keyword);
                                if (!empty($keyword)):
                            ?>
                                <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>)">
                        Add to Cart
                    </button>
                    <a href="all_product.php" class="btn btn-secondary">
                        Continue Shopping
                    </a>
                </div>
                
                <div class="product-stats">
                    <div class="stats-title">Product Information</div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $product['id']; ?></div>
                            <div class="stat-label">Product ID</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo date('M j, Y', strtotime($product['created_at'])); ?></div>
                            <div class="stat-label">Added Date</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $product['category_name']; ?></div>
                            <div class="stat-label">Category</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $product['brand_name']; ?></div>
                            <div class="stat-label">Brand</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart successfully!');
                } else {
                    alert(data.message || 'Failed to add product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product to cart');
            });
        }
    </script>
</body>
</html>
