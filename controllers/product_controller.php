<?php
require_once '../models/product_class.php';

class ProductController {
    
    public function add_product_ctr($args) {
        $product = new Product();
        return $product->add($args);
    }
    
    public function get_products_ctr($user_id) {
        $product = new Product();
        return $product->get_all_by_user($user_id);
    }
    
    public function get_product_ctr($id, $user_id) {
        $product = new Product();
        return $product->get_by_id($id, $user_id);
    }
    
    public function edit_product_ctr($id, $args, $user_id) {
        $product = new Product();
        return $product->edit($id, $args, $user_id);
    }
    
    public function delete_product_ctr($id, $user_id) {
        $product = new Product();
        return $product->delete($id, $user_id);
    }
    
    // Public controller methods
    public function view_all_products_ctr($limit = 10, $offset = 0) {
        $product = new Product();
        return $product->view_all_products($limit, $offset);
    }
    
    public function get_total_products_count_ctr() {
        $product = new Product();
        return $product->get_total_products_count();
    }
    
    public function search_products_ctr($query, $limit = 10, $offset = 0) {
        $product = new Product();
        return $product->search_products($query, $limit, $offset);
    }
    
    public function get_search_count_ctr($query) {
        $product = new Product();
        return $product->get_search_count($query);
    }
    
    public function filter_products_by_category_ctr($cat_id, $limit = 10, $offset = 0) {
        $product = new Product();
        return $product->filter_products_by_category($cat_id, $limit, $offset);
    }
    
    public function filter_products_by_brand_ctr($brand_id, $limit = 10, $offset = 0) {
        $product = new Product();
        return $product->filter_products_by_brand($brand_id, $limit, $offset);
    }
    
    public function view_single_product_ctr($id) {
        $product = new Product();
        return $product->view_single_product($id);
    }
    
    public function composite_search_ctr($filters = [], $limit = 10, $offset = 0) {
        $product = new Product();
        return $product->composite_search($filters, $limit, $offset);
    }
    
    public function get_composite_search_count_ctr($filters = []) {
        $product = new Product();
        return $product->get_composite_search_count($filters);
    }
}
?>
