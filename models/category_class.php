<?php
require_once '../config.php';

class Category extends DatabaseConnection {
    
    public function add($args) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $name = trim($args['name']);
        $created_by = $args['created_by'];
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Category name must be 100 characters or less'];
        }
        
        try {
            // Check if category name already exists
            $check_query = "SELECT id FROM categories WHERE name = :name";
            $check_stmt = $connection->prepare($check_query);
            $check_stmt->bindParam(':name', $name);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Category name already exists'];
            }
            
            $query = "INSERT INTO categories (name, created_by, created_at) VALUES (:name, :created_by, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':created_by', $created_by);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Category created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create category'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function get_all_by_user($user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM categories WHERE created_by = :user_id ORDER BY created_at DESC";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function get_by_id($id, $user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM categories WHERE id = :id AND created_by = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
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
    
    public function edit($id, $args, $user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $name = trim($args['name']);
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Category name must be 100 characters or less'];
        }
        
        try {
            // Check if category name already exists (excluding current category)
            $check_query = "SELECT id FROM categories WHERE name = :name AND id != :id";
            $check_stmt = $connection->prepare($check_query);
            $check_stmt->bindParam(':name', $name);
            $check_stmt->bindParam(':id', $id);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Category name already exists'];
            }
            
            $query = "UPDATE categories SET name = :name, updated_at = NOW() WHERE id = :id AND created_by = :user_id";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Category updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Category not found or update failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function delete($id, $user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        try {
            $query = "DELETE FROM categories WHERE id = :id AND created_by = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Category deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Category not found or delete failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>
