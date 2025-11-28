<?php
require_once '../config.php';

class Cart extends DatabaseConnection {
    
    public function add_to_cart($user_id, $product_id, $quantity = 1) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            // First check if product exists
            $product_check = "SELECT id FROM products WHERE id = :product_id";
            $product_stmt = $connection->prepare($product_check);
            $product_stmt->bindParam(':product_id', $product_id);
            $product_stmt->execute();
            
            if ($product_stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            
            // Check if item already exists in cart
            $check_query = "SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
            $check_stmt = $connection->prepare($check_query);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->bindParam(':product_id', $product_id);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                // Update quantity if item exists
                $update_query = "UPDATE cart_items SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
                $update_stmt = $connection->prepare($update_query);
                $update_stmt->bindParam(':user_id', $user_id);
                $update_stmt->bindParam(':product_id', $product_id);
                $update_stmt->bindParam(':quantity', $quantity);
                $update_stmt->execute();
                
                return ['success' => true, 'message' => 'Cart updated successfully'];
            } else {
                // Add new item to cart
                $query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
                $stmt = $connection->prepare($query);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $quantity);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Product added to cart successfully'];
                } else {
                    return ['success' => false, 'message' => 'Failed to add product to cart'];
                }
            }
            
        } catch(PDOException $e) {
            // More detailed error message
            $error_msg = $e->getMessage();
            if (strpos($error_msg, 'foreign key constraint') !== false) {
                return ['success' => false, 'message' => 'Product not found in database'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $error_msg];
        }
    }
    
    public function get_cart_items($user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT ci.*, p.title, p.price, p.image_path 
                     FROM cart_items ci 
                     JOIN products p ON ci.product_id = p.id 
                     WHERE ci.user_id = :user_id 
                     ORDER BY ci.created_at DESC";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function update_quantity($cart_item_id, $user_id, $quantity) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "UPDATE cart_items SET quantity = :quantity WHERE id = :id AND user_id = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $cart_item_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':quantity', $quantity);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Quantity updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update quantity'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function remove_from_cart($cart_item_id, $user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "DELETE FROM cart_items WHERE id = :id AND user_id = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $cart_item_id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            } else {
                return ['success' => false, 'message' => 'Failed to remove item'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function clear_cart($user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "DELETE FROM cart_items WHERE user_id = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Cart cleared'];
            } else {
                return ['success' => false, 'message' => 'Failed to clear cart'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}

