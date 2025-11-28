<?php
require_once '../config.php';

class Product extends DatabaseConnection {
    
    public function add($args) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $title = trim($args['title']);
        $description = trim($args['description']);
        $price = $args['price'];
        $category_id = $args['category_id'];
        $brand_id = $args['brand_id'];
        $keyword = trim($args['keyword']);
        $image_path = $args['image_path'] ?? null;
        $created_by = $args['created_by'];
        
        if (empty($title)) {
            return ['success' => false, 'message' => 'Product title is required'];
        }
        
        if (strlen($title) > 200) {
            return ['success' => false, 'message' => 'Product title must be 200 characters or less'];
        }
        
        if (empty($price) || !is_numeric($price) || $price <= 0) {
            return ['success' => false, 'message' => 'Valid price is required'];
        }
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category is required'];
        }
        
        if (empty($brand_id)) {
            return ['success' => false, 'message' => 'Brand is required'];
        }
        
        try {
            $query = "INSERT INTO products (title, description, price, category_id, brand_id, keyword, image_path, created_by, created_at) 
                     VALUES (:title, :description, :price, :category_id, :brand_id, :keyword, :image_path, :created_by, NOW())";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':brand_id', $brand_id);
            $stmt->bindParam(':keyword', $keyword);
            $stmt->bindParam(':image_path', $image_path);
            $stmt->bindParam(':created_by', $created_by);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Product created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create product'];
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
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.created_by = :user_id 
                     ORDER BY c.name, b.name, p.title";
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
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.id = :id AND p.created_by = :user_id";
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
        
        $title = trim($args['title']);
        $description = trim($args['description']);
        $price = $args['price'];
        $category_id = $args['category_id'];
        $brand_id = $args['brand_id'];
        $keyword = trim($args['keyword']);
        $image_path = $args['image_path'] ?? null;
        
        if (empty($title)) {
            return ['success' => false, 'message' => 'Product title is required'];
        }
        
        if (strlen($title) > 200) {
            return ['success' => false, 'message' => 'Product title must be 200 characters or less'];
        }
        
        if (empty($price) || !is_numeric($price) || $price <= 0) {
            return ['success' => false, 'message' => 'Valid price is required'];
        }
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category is required'];
        }
        
        if (empty($brand_id)) {
            return ['success' => false, 'message' => 'Brand is required'];
        }
        
        try {
            $query = "UPDATE products SET title = :title, description = :description, price = :price, 
                     category_id = :category_id, brand_id = :brand_id, keyword = :keyword, 
                     image_path = :image_path, updated_at = NOW() 
                     WHERE id = :id AND created_by = :user_id";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':brand_id', $brand_id);
            $stmt->bindParam(':keyword', $keyword);
            $stmt->bindParam(':image_path', $image_path);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Product updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Product not found or update failed'];
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
            $query = "DELETE FROM products WHERE id = :id AND created_by = :user_id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Product deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Product not found or delete failed'];
            }
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    // Public methods
    public function view_all_products($limit = 10, $offset = 0) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function get_total_products_count() {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return 0;
        }
        
        try {
            $query = "SELECT COUNT(*) as total FROM products";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    public function search_products($query, $limit = 10, $offset = 0) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $search_term = '%' . $query . '%';
            $sql_query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                         FROM products p 
                         JOIN categories c ON p.category_id = c.id 
                         JOIN brands b ON p.brand_id = b.id 
                         WHERE p.title LIKE :search_term 
                         OR p.description LIKE :search_term 
                         OR p.keyword LIKE :search_term 
                         ORDER BY p.created_at DESC 
                         LIMIT :limit OFFSET :offset";
            
            $stmt = $connection->prepare($sql_query);
            $stmt->bindParam(':search_term', $search_term);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function get_search_count($query) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return 0;
        }
        
        try {
            $search_term = '%' . $query . '%';
            $query = "SELECT COUNT(*) as total 
                     FROM products p 
                     WHERE p.title LIKE :search_term 
                     OR p.description LIKE :search_term 
                     OR p.keyword LIKE :search_term";
            
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':search_term', $search_term);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    public function filter_products_by_category($cat_id, $limit = 10, $offset = 0) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.category_id = :cat_id 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':cat_id', $cat_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function filter_products_by_brand($brand_id, $limit = 10, $offset = 0) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.brand_id = :brand_id 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':brand_id', $brand_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function view_single_product($id) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.id = :id";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':id', $id);
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
    
    // Advanced search functionality
    public function composite_search($filters = [], $limit = 10, $offset = 0) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return false;
        }
        
        try {
            $where_conditions = [];
            $params = [];
            
            // Build dynamic WHERE clause based on filters
            if (!empty($filters['search'])) {
                $where_conditions[] = "(p.title LIKE :search OR p.description LIKE :search OR p.keyword LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['category_id'])) {
                $where_conditions[] = "p.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            
            if (!empty($filters['brand_id'])) {
                $where_conditions[] = "p.brand_id = :brand_id";
                $params[':brand_id'] = $filters['brand_id'];
            }
            
            if (!empty($filters['min_price'])) {
                $where_conditions[] = "p.price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $where_conditions[] = "p.price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name 
                     FROM products p 
                     JOIN categories c ON p.category_id = c.id 
                     JOIN brands b ON p.brand_id = b.id 
                     $where_clause
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $connection->prepare($query);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function get_composite_search_count($filters = []) {
        $connection = $this->getConnection();
        
        if (!$connection) {
            return 0;
        }
        
        try {
            $where_conditions = [];
            $params = [];
            
            // Build dynamic WHERE clause based on filters
            if (!empty($filters['search'])) {
                $where_conditions[] = "(p.title LIKE :search OR p.description LIKE :search OR p.keyword LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['category_id'])) {
                $where_conditions[] = "p.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            
            if (!empty($filters['brand_id'])) {
                $where_conditions[] = "p.brand_id = :brand_id";
                $params[':brand_id'] = $filters['brand_id'];
            }
            
            if (!empty($filters['min_price'])) {
                $where_conditions[] = "p.price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }
            
            if (!empty($filters['max_price'])) {
                $where_conditions[] = "p.price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }
            
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
            
            $query = "SELECT COUNT(*) as total 
                     FROM products p 
                     $where_clause";
            
            $stmt = $connection->prepare($query);
            
            // Bind all parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
            
        } catch(PDOException $e) {
            return 0;
        }
    }
}
?>
