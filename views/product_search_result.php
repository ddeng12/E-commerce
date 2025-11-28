<?php
require_once '../core.php';
require_once '../actions/product_actions.php';

// Get search parameters
$search_query = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$brand_id = $_GET['brand_id'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Determine which action to use based on parameters
if (!empty($search_query) || !empty($category_id) || !empty($brand_id) || !empty($min_price) || !empty($max_price)) {
    $_GET['action'] = 'composite_search';
} else {
    $_GET['action'] = 'search';
}

// Get search results
$productActions = new ProductActions();
$result = $productActions->handleRequest();

$products = $result['success'] ? $result['data'] : [];
$pagination = $result['pagination'] ?? null;
$filters = $result['filters'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - E-commerce Store</title>
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
            max-width: 1200px;
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
        
        .header h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5em;
            font-weight: 300;
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
        
        .search-info {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .search-info h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .search-criteria {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .criteria-item {
            background: rgba(102, 126, 234, 0.2);
            color: #333;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .results-count {
            font-size: 1.1em;
            color: #667eea;
            font-weight: 600;
        }
        
        .refine-search {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .refine-search h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3em;
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
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            color: #666;
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
            <h1>Search Results</h1>
            
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
        
        <div class="search-info">
            <h2>Search Results</h2>
            <div class="search-criteria">
                <?php if (!empty($search_query)): ?>
                    <span class="criteria-item">Search: "<?php echo htmlspecialchars($search_query); ?>"</span>
                <?php endif; ?>
                <?php if (!empty($category_id)): ?>
                    <span class="criteria-item">Category: <?php echo htmlspecialchars($category_id); ?></span>
                <?php endif; ?>
                <?php if (!empty($brand_id)): ?>
                    <span class="criteria-item">Brand: <?php echo htmlspecialchars($brand_id); ?></span>
                <?php endif; ?>
                <?php if (!empty($min_price)): ?>
                    <span class="criteria-item">Min Price: $<?php echo htmlspecialchars($min_price); ?></span>
                <?php endif; ?>
                <?php if (!empty($max_price)): ?>
                    <span class="criteria-item">Max Price: $<?php echo htmlspecialchars($max_price); ?></span>
                <?php endif; ?>
            </div>
            <div class="results-count">
                <?php if ($result['success']): ?>
                    Found <?php echo count($products); ?> product(s)
                    <?php if ($pagination && $pagination['total_count'] > count($products)): ?>
                        of <?php echo $pagination['total_count']; ?> total
                    <?php endif; ?>
                <?php else: ?>
                    No results found
                <?php endif; ?>
            </div>
        </div>
        
        <div class="refine-search">
            <h3>Refine Your Search</h3>
            <form class="search-form" method="GET" action="product_search_result.php">
                <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
                <select name="category_id">
                    <option value="">All Categories</option>
                    <!-- Categories will be populated by JavaScript -->
                </select>
                <select name="brand_id">
                    <option value="">All Brands</option>
                    <!-- Brands will be populated by JavaScript -->
                </select>
                <input type="number" name="min_price" placeholder="Min Price" step="0.01" min="0" value="<?php echo htmlspecialchars($min_price); ?>">
                <input type="number" name="max_price" placeholder="Max Price" step="0.01" min="0" value="<?php echo htmlspecialchars($max_price); ?>">
                <button type="submit" class="btn">Search</button>
                <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
            </form>
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
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])); ?>">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $pagination['current_page']): ?>
                                <span class="current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])); ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-results">
                    <h3>No products found</h3>
                    <p>Try adjusting your search criteria or browse all products.</p>
                    <a href="all_product.php" class="btn" style="margin-top: 20px; display: inline-block;">Browse All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/product_display.js"></script>
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
        
        function clearFilters() {
            window.location.href = 'all_product.php';
        }
    </script>
</body>
</html>
