<?php
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

class ProductActions {
    private $productController;
    private $categoryController;
    private $brandController;
    
    public function __construct() {
        $this->productController = new ProductController();
        $this->categoryController = new CategoryController();
        $this->brandController = new BrandController();
    }
    
    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? 'view_all';
        
        switch ($action) {
            case 'view_all':
                return $this->viewAllProducts();
            case 'view_single':
                return $this->viewSingleProduct();
            case 'search':
                return $this->searchProducts();
            case 'filter_category':
                return $this->filterByCategory();
            case 'filter_brand':
                return $this->filterByBrand();
            case 'composite_search':
                return $this->compositeSearch();
            case 'get_categories':
                return $this->getCategories();
            case 'get_brands':
                return $this->getBrands();
            case 'get_brands_by_category':
                return $this->getBrandsByCategory();
            default:
                return $this->viewAllProducts();
        }
    }
    
    private function viewAllProducts() {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productController->view_all_products_ctr($limit, $offset);
        $totalCount = $this->productController->get_total_products_count_ctr();
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'success' => true,
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    }
    
    private function viewSingleProduct() {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            return ['success' => false, 'message' => 'Product ID is required'];
        }
        
        $product = $this->productController->view_single_product_ctr($id);
        
        if ($product) {
            return ['success' => true, 'data' => $product];
        } else {
            return ['success' => false, 'message' => 'Product not found'];
        }
    }
    
    private function searchProducts() {
        $query = trim($_GET['q'] ?? $_POST['q'] ?? '');
        
        if (empty($query)) {
            return ['success' => false, 'message' => 'Search query is required'];
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productController->search_products_ctr($query, $limit, $offset);
        $totalCount = $this->productController->get_search_count_ctr($query);
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'success' => true,
            'data' => $products,
            'query' => $query,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    }
    
    private function filterByCategory() {
        $category_id = $_GET['category_id'] ?? '';
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category ID is required'];
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productController->filter_products_by_category_ctr($category_id, $limit, $offset);
        
        return [
            'success' => true,
            'data' => $products,
            'filter' => ['category_id' => $category_id],
            'pagination' => [
                'current_page' => $page,
                'limit' => $limit
            ]
        ];
    }
    
    private function filterByBrand() {
        $brand_id = $_GET['brand_id'] ?? '';
        
        if (empty($brand_id)) {
            return ['success' => false, 'message' => 'Brand ID is required'];
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productController->filter_products_by_brand_ctr($brand_id, $limit, $offset);
        
        return [
            'success' => true,
            'data' => $products,
            'filter' => ['brand_id' => $brand_id],
            'pagination' => [
                'current_page' => $page,
                'limit' => $limit
            ]
        ];
    }
    
    private function compositeSearch() {
        $filters = [
            'search' => trim($_GET['q'] ?? $_GET['search'] ?? $_POST['q'] ?? $_POST['search'] ?? ''),
            'category_id' => $_GET['category_id'] ?? $_POST['category_id'] ?? '',
            'brand_id' => $_GET['brand_id'] ?? $_POST['brand_id'] ?? '',
            'min_price' => $_GET['min_price'] ?? $_POST['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? $_POST['max_price'] ?? ''
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });
        
        if (empty($filters)) {
            return ['success' => false, 'message' => 'At least one filter is required'];
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productController->composite_search_ctr($filters, $limit, $offset);
        $totalCount = $this->productController->get_composite_search_count_ctr($filters);
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'success' => true,
            'data' => $products,
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit
            ]
        ];
    }
    
    private function getCategories() {
        // Get all categories (not user-specific for customer view)
        $connection = new DatabaseConnection();
        $conn = $connection->getConnection();
        
        try {
            $query = "SELECT * FROM categories ORDER BY name";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'data' => $categories];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    private function getBrands() {
        // Get all brands (not user-specific for customer view)
        $connection = new DatabaseConnection();
        $conn = $connection->getConnection();
        
        try {
            $query = "SELECT b.*, c.name as category_name 
                     FROM brands b 
                     JOIN categories c ON b.category_id = c.id 
                     ORDER BY c.name, b.name";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'data' => $brands];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    private function getBrandsByCategory() {
        $category_id = $_GET['category_id'] ?? '';
        
        if (empty($category_id)) {
            return ['success' => false, 'message' => 'Category ID is required'];
        }
        
        $connection = new DatabaseConnection();
        $conn = $connection->getConnection();
        
        try {
            $query = "SELECT * FROM brands WHERE category_id = :category_id ORDER BY name";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'data' => $brands];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    $productActions = new ProductActions();
    $result = $productActions->handleRequest();
    echo json_encode($result);
    exit;
}
?>
