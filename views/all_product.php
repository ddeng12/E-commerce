<?php
require_once '../core.php';
require_once '../actions/product_actions.php';

// Get products data
$productActions = new ProductActions();
$result = $productActions->handleRequest();

$products = $result['success'] ? $result['data'] : [];
$pagination = $result['pagination'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - E-commerce Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 40px 30px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: #2d3748;
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .nav a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .search-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .search-form input, .search-form select {
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 200px;
        }
        
        .search-form input:focus, .search-form select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .product-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .product-price {
            font-size: 1.5em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }
        
        .product-category, .product-brand {
            background: rgba(102, 126, 234, 0.1);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .add-to-cart {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .pagination a, .pagination span {
            padding: 10px 15px;
            border-radius: 25px;
            text-decoration: none;
            color: #667eea;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
        }
        
        .no-products {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            color: #666;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: white;
            font-size: 18px;
        }
        
        .product-id {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .search-form input, .search-form select {
                min-width: 100%;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
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
            <h1>Our Products</h1>
            
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
        
        <div class="search-section">
            <form class="search-form" id="searchForm">
                <input type="text" name="search" placeholder="Search products..." id="searchInput">
                <select name="category_id" id="categoryFilter">
                    <option value="">All Categories</option>
                </select>
                <select name="brand_id" id="brandFilter">
                    <option value="">All Brands</option>
                </select>
                <input type="number" name="min_price" placeholder="Min Price" step="0.01" min="0">
                <input type="number" name="max_price" placeholder="Max Price" step="0.01" min="0">
                <button type="submit" class="btn">Search</button>
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
            </form>
            
            <div class="filters">
                <div class="filter-group">
                    <label>Quick Filters:</label>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="btn" onclick="filterByCategory('')">All Products</button>
                        <button class="btn" onclick="filterByCategory('1')">Electronics</button>
                        <button class="btn" onclick="filterByCategory('2')">Clothing</button>
                        <button class="btn" onclick="filterByCategory('3')">Footwear</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="productsContainer">
            <?php if ($result['success'] && !empty($products)): ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-id">#<?php echo htmlspecialchars($product['id']); ?></div>
                            <?php if ($product['image_path']): ?>
                                <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-image"></div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                
                                <div class="product-meta">
                                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                    <span class="product-brand"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                                </div>
                                
                                <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($pagination && $pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <a href="?page=<?php echo $pagination['current_page'] - 1; ?>">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $pagination['current_page']): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?page=<?php echo $pagination['current_page'] + 1; ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-products">
                    <h3>No products found</h3>
                    <p>Try adjusting your search criteria or browse all products.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/product_display.js"></script>
</body>
</html>
