<?php
require_once '../models/category_class.php';

class CategoryController {
    
    public function add_category_ctr($args) {
        $category = new Category();
        return $category->add($args);
    }
    
    public function get_categories_ctr($user_id) {
        $category = new Category();
        return $category->get_all_by_user($user_id);
    }
    
    public function get_category_ctr($id, $user_id) {
        $category = new Category();
        return $category->get_by_id($id, $user_id);
    }
    
    public function edit_category_ctr($id, $args, $user_id) {
        $category = new Category();
        return $category->edit($id, $args, $user_id);
    }
    
    public function delete_category_ctr($id, $user_id) {
        $category = new Category();
        return $category->delete($id, $user_id);
    }
}
?>
