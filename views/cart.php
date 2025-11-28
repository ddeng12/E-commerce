<?php
require_once '../core.php';
require_once '../controllers/cart_controller.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get cart items for logged in user
$cartItems = [];
$total = 0;
$controller = new CartController();
$cartItems = $controller->get_cart_items_ctr($_SESSION['user_id']);

// Calculate total
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
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
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: #2d3748;
            font-size: 3em;
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
            flex-wrap: wrap;
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
        
        .cart-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-cart h2 {
            color: #2d3748;
            font-size: 2em;
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            color: #718096;
            font-size: 1.2em;
            margin-bottom: 40px;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table thead {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .cart-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
        }
        
        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .cart-item-row {
            transition: opacity 0.3s ease;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .cart-item-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: #667eea;
            font-weight: 600;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: #667eea;
            transition: all 0.2s ease;
        }
        
        .quantity-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
        }
        
        .quantity-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .subtotal {
            font-weight: 700;
            color: #2d3748;
            font-size: 1.1em;
        }
        
        .btn-remove-item {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-remove-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .cart-summary {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 16px;
            padding: 30px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .summary-row.total {
            font-size: 1.5em;
            font-weight: 700;
            color: #667eea;
            padding-top: 15px;
            border-top: 2px solid rgba(102, 126, 234, 0.2);
            margin-top: 15px;
        }
        
        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        @media (max-width: 768px) {
            .cart-table {
                font-size: 14px;
            }
            
            .cart-table th,
            .cart-table td {
                padding: 10px 5px;
            }
            
            .cart-item-image {
                width: 60px;
                height: 60px;
            }
            
            .cart-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
    <script src="../assets/cart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Shopping Cart</h1>
            
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="all_product.php">All Products</a>
                <?php if (isLoggedIn()): ?>
                    <a href="cart.php">Cart</a>
                    <?php if (isAdmin()): ?>
                        <a href="product.php">Add Product</a>
                        <a href="brand.php">Brands</a>
                        <a href="category.php">Categories</a>
                    <?php endif; ?>
                    <a href="../assets/logout.php">Logout</a>
                <?php else: ?>
                    <a href="register.php">Register</a>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
        
        <div class="cart-section">
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items yet.</p>
                    <a href="all_product.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <h2 style="margin-bottom: 20px; color: #2d3748;">Cart Items (<?php echo count($cartItems); ?>)</h2>
                
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                            <tr class="cart-item-row">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <?php if ($item['image_path']): ?>
                                            <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                 class="cart-item-image">
                                        <?php else: ?>
                                            <div class="cart-item-image"></div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></span>
                                </td>
                                <td>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" data-action="decrease" data-cart-item-id="<?php echo $item['id']; ?>">-</button>
                                        <input type="number" 
                                               class="quantity-input" 
                                               value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                               min="1"
                                               data-cart-item-id="<?php echo $item['id']; ?>">
                                        <button class="quantity-btn" data-action="increase" data-cart-item-id="<?php echo $item['id']; ?>">+</button>
                                    </div>
                                </td>
                                <td>
                                    <span class="subtotal">$<?php echo number_format($subtotal, 2); ?></span>
                                </td>
                                <td>
                                    <button class="btn-remove-item" data-cart-item-id="<?php echo $item['id']; ?>">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <a href="all_product.php" class="btn btn-secondary">Continue Shopping</a>
                    <button id="emptyCartBtn" class="btn btn-danger">Empty Cart</button>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
