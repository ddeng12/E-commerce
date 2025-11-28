<?php
require_once '../config.php';

class Order extends DatabaseConnection {
    
    /**
     * Create a new order and return its ID
     * @param array $params Array containing customer_id, order_reference, total_amount, status
     * @return array|false Returns order data with ID on success, false on failure
     */
    public function create_order($params) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $customer_id = $params['customer_id'];
        $order_reference = $params['order_reference'];
        $total_amount = $params['total_amount'];
        $status = $params['status'] ?? 'pending';
        
        try {
            $query = "INSERT INTO orders (customer_id, order_reference, total_amount, status, created_at) 
                     VALUES (:customer_id, :order_reference, :total_amount, :status, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':order_reference', $order_reference);
            $stmt->bindParam(':total_amount', $total_amount);
            $stmt->bindParam(':status', $status);
            
            if ($stmt->execute()) {
                $order_id = $connection->lastInsertId();
                return [
                    'success' => true,
                    'order_id' => $order_id,
                    'order_reference' => $order_reference,
                    'message' => 'Order created successfully'
                ];
            } else {
                return ['success' => false, 'message' => 'Failed to create order'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Add order details (product items) to an order
     * @param array $params Array containing order_id, product_id, quantity, price, subtotal
     * @return array Returns success status and message
     */
    public function add_order_details($params) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $order_id = $params['order_id'];
        $product_id = $params['product_id'];
        $quantity = $params['quantity'];
        $price = $params['price'];
        $subtotal = $params['subtotal'];
        
        try {
            $query = "INSERT INTO orderdetails (order_id, product_id, quantity, price, subtotal, created_at) 
                     VALUES (:order_id, :product_id, :quantity, :price, :subtotal, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':subtotal', $subtotal);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Order detail added successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to add order detail'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Record a payment for an order
     * @param array $params Array containing order_id, payment_method, amount, payment_status, transaction_reference
     * @return array Returns success status and message
     */
    public function record_payment($params) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $order_id = $params['order_id'];
        $payment_method = $params['payment_method'] ?? 'simulated';
        $amount = $params['amount'];
        $payment_status = $params['payment_status'] ?? 'completed';
        $transaction_reference = $params['transaction_reference'] ?? null;
        
        try {
            $query = "INSERT INTO payments (order_id, payment_method, amount, payment_status, transaction_reference, created_at) 
                     VALUES (:order_id, :payment_method, :amount, :payment_status, :transaction_reference, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_status', $payment_status);
            $stmt->bindParam(':transaction_reference', $transaction_reference);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Payment recorded successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to record payment'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all orders for a customer
     * @param int $customer_id The customer ID
     * @return array|false Returns array of orders or false on failure
     */
    public function get_customer_orders($customer_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT o.*, 
                     COUNT(od.id) as item_count,
                     SUM(od.subtotal) as total
                     FROM orders o
                     LEFT JOIN orderdetails od ON o.id = od.order_id
                     WHERE o.customer_id = :customer_id
                     GROUP BY o.id
                     ORDER BY o.created_at DESC";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get order details with product information
     * @param int $order_id The order ID
     * @return array|false Returns array of order details or false on failure
     */
    public function get_order_details($order_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT od.*, p.title, p.image_path 
                     FROM orderdetails od
                     JOIN products p ON od.product_id = p.id
                     WHERE od.order_id = :order_id
                     ORDER BY od.created_at ASC";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get payment information for an order
     * @param int $order_id The order ID
     * @return array|false Returns payment data or false on failure
     */
    public function get_order_payment($order_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM payments WHERE order_id = :order_id ORDER BY created_at DESC LIMIT 1";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    /**
     * Generate a unique order reference
     * @return string Unique order reference
     */
    public function generate_order_reference() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
    
    /**
     * Generate a unique transaction reference
     * @return string Unique transaction reference
     */
    public function generate_transaction_reference() {
        return 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

