<?php
require_once 'config.php';

class Customer extends DatabaseConnection {
    
    public function add($args) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $full_name = trim($args['full_name']);
        $email = trim($args['email']);
        $password = $args['password'];
        $country = trim($args['country']);
        $city = trim($args['city']);
        $contact_number = trim($args['contact_number']);
        $user_role = isset($args['user_role']) ? (int)$args['user_role'] : 2;
        $image = isset($args['image']) ? $args['image'] : null;
        
        if (empty($full_name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($contact_number)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        if (!preg_match('/^[0-9+\-\s()]+$/', $contact_number)) {
            return ['success' => false, 'message' => 'Invalid contact number format'];
        }
        
        try {
            $check_email_query = "SELECT id FROM customers WHERE email = :email";
            $check_stmt = $connection->prepare($check_email_query);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO customers (full_name, email, password, country, city, contact_number, user_role, image, created_at) 
                     VALUES (:full_name, :email, :password, :country, :city, :contact_number, :user_role, :image, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':country', $country);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':user_role', $user_role);
            $stmt->bindParam(':image', $image);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Customer registered successfully'];
            } else {
                return ['success' => false, 'message' => 'Registration failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function edit($id, $args) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "UPDATE customers SET full_name = :full_name, email = :email, country = :country, 
                     city = :city, contact_number = :contact_number, image = :image, updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':full_name', $args['full_name']);
            $stmt->bindParam(':email', $args['email']);
            $stmt->bindParam(':country', $args['country']);
            $stmt->bindParam(':city', $args['city']);
            $stmt->bindParam(':contact_number', $args['contact_number']);
            $stmt->bindParam(':image', $args['image']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Customer updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Update failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function get_customer_by_email($email) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM customers WHERE email = :email";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "DELETE FROM customers WHERE id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Customer deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Delete failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>
