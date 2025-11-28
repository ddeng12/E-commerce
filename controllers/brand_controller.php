<?php
require_once '../models/brand_class.php';

class BrandController {
    
    public function add_brand_ctr($args) {
        $brand = new Brand();
        return $brand->add($args);
    }
    
    public function get_brands_ctr($user_id) {
        $brand = new Brand();
        return $brand->get_all_by_user($user_id);
    }
    
    public function get_brand_ctr($id, $user_id) {
        $brand = new Brand();
        return $brand->get_by_id($id, $user_id);
    }
    
    public function edit_brand_ctr($id, $args, $user_id) {
        $brand = new Brand();
        return $brand->edit($id, $args, $user_id);
    }
    
    public function delete_brand_ctr($id, $user_id) {
        $brand = new Brand();
        return $brand->delete($id, $user_id);
    }
    
    public function get_brands_by_category_ctr($category_id, $user_id) {
        $brand = new Brand();
        return $brand->get_brands_by_category($category_id, $user_id);
    }
}
?>
