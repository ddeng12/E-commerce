<?php
require_once '../config.php';

class Brand extends DatabaseConnection {
    
    public function add($args) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $name = trim($args['name']);
        $category_id = $args['category_id'];
        $created_by = $args['created_by'];
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Brand name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Brand name must be 100 characters or less'];
        }
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category is required'];
        }
        
        try {
            // Check if brand + category combination already exists
            $check_query = "SELECT id FROM brands WHERE name = :name AND category_id = :category_id";
            $check_stmt = $connection->prepare($check_query);
            $check_stmt->bindParam(':name', $name);
            $check_stmt->bindParam(':category_id', $category_id);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Brand name already exists in this category'];
            }
            
            $query = "INSERT INTO brands (name, category_id, created_by, created_at) VALUES (:name, :category_id, :created_by, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':created_by', $created_by);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Brand created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create brand'];
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
            $query = "SELECT b.*, c.name as category_name 
                     FROM brands b 
                     JOIN categories c ON b.category_id = c.id 
                     WHERE b.created_by = :user_id 
                     ORDER BY c.name, b.name";
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
            $query = "SELECT b.*, c.name as category_name 
                     FROM brands b 
                     JOIN categories c ON b.category_id = c.id 
                     WHERE b.id = :id AND b.created_by = :user_id";
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
        $category_id = $args['category_id'];
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Brand name is required'];
        }
        
        if (strlen($name) > 100) {
            return ['success' => false, 'message' => 'Brand name must be 100 characters or less'];
        }
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category is required'];
        }
        
        try {
            // Check if brand + category combination already exists (excluding current brand)
            $check_query = "SELECT id FROM brands WHERE name = :name AND category_id = :category_id AND id != :id";
            $check_stmt = $connection->prepare($check_query);
            $check_stmt->bindParam(':name', $name);
            $check_stmt->bindParam(':category_id', $category_id);
            $check_stmt->bindParam(':id', $id);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Brand name already exists in this category'];
            }
            
            $query = "UPDATE brands SET name = :name, category_id = :category_id, updated_at = NOW() WHERE id = :id AND created_by = :user_id";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Brand updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Brand not found or update failed'];
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
            $query = "DELETE FROM brands WHERE id = :id AND created_by = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Brand deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Brand not found or delete failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function get_brands_by_category($category_id, $user_id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM brands WHERE category_id = :category_id AND created_by = :user_id ORDER BY name";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
