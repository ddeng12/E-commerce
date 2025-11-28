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

// Check if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Check for success message
$success = isset($_GET['success']) && $_GET['success'] == '1';
$order_ref = $_GET['order_ref'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-commerce Store</title>
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
        
        .checkout-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .order-summary {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .order-summary h2 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        
        .order-item {
            display: flex;
            gap: 15px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .order-item-info {
            flex: 1;
        }
        
        .order-item-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .order-item-meta {
            color: #718096;
            font-size: 0.9em;
        }
        
        .order-item-price {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1em;
        }
        
        .payment-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .payment-section h2 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #2d3748;
        }
        
        .summary-row.total {
            font-size: 1.5em;
            font-weight: 700;
            color: #667eea;
            padding-top: 15px;
            border-top: 2px solid rgba(102, 126, 234, 0.2);
            margin-top: 15px;
        }
        
        .btn {
            width: 100%;
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
            text-align: center;
            margin-top: 20px;
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
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Payment Modal Styles */
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            color: #2d3748;
            font-size: 1.8em;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 2em;
            color: #718096;
            cursor: pointer;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .modal-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #2d3748;
        }
        
        .modal-body {
            margin-bottom: 30px;
            color: #4a5568;
            line-height: 1.6;
        }
        
        .payment-info {
            background: rgba(102, 126, 234, 0.05);
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }
        
        .payment-info p {
            margin-bottom: 10px;
        }
        
        .modal-footer {
            display: flex;
            gap: 15px;
        }
        
        .modal-footer .btn {
            margin-top: 0;
            flex: 1;
        }
        
        .checkout-success-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        .success-content {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: white;
            margin: 0 auto 20px;
        }
        
        .success-content h2 {
            color: #2d3748;
            margin-bottom: 20px;
        }
        
        .success-content p {
            color: #4a5568;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .checkout-error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            z-index: 10002;
            animation: slideIn 0.3s ease;
        }
        
        .error-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .error-icon {
            font-size: 2em;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                padding: 30px 20px;
            }
        }
    </style>
    <script src="../assets/checkout.js?v=20251112"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Checkout</h1>
            
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="all_product.php">All Products</a>
                <a href="cart.php">Cart</a>
                <?php if (isLoggedIn()): ?>
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
        
        <?php if ($success && $order_ref): ?>
            <div class="checkout-success-message" style="display: flex;">
                <div class="success-content">
                    <div class="success-icon">✓</div>
                    <h2>Order Placed Successfully!</h2>
                    <p><strong>Order Reference:</strong> <?php echo htmlspecialchars($order_ref); ?></p>
                    <p>Thank you for your purchase!</p>
                    <a href="all_product.php" class="btn btn-primary" style="margin-top: 20px;">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="checkout-content">
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <?php foreach ($cartItems as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                    ?>
                        <div class="order-item">
                            <?php if ($item['image_path']): ?>
                                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                     class="order-item-image">
                            <?php else: ?>
                                <div class="order-item-image"></div>
                            <?php endif; ?>
                            
                            <div class="order-item-info">
                                <div class="order-item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                <div class="order-item-meta">
                                    Quantity: <?php echo htmlspecialchars($item['quantity']); ?> × $<?php echo number_format($item['price'], 2); ?>
                                </div>
                            </div>
                            
                            <div class="order-item-price">
                                $<?php echo number_format($subtotal, 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="payment-section">
                    <h2>Payment Summary</h2>
                    
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
                    
                    <button id="completePaymentBtn" class="btn btn-primary">Confirm Payment</button>
                    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

