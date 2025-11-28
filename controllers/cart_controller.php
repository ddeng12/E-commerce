<?php
require_once '../models/cart_class.php';

class CartController {
    
    public function add_to_cart_ctr($user_id, $product_id, $quantity = 1) {
        $cart = new Cart();
        return $cart->add_to_cart($user_id, $product_id, $quantity);
    }
    
    public function get_cart_items_ctr($user_id) {
        $cart = new Cart();
        return $cart->get_cart_items($user_id);
    }
    
    public function update_quantity_ctr($cart_item_id, $user_id, $quantity) {
        $cart = new Cart();
        return $cart->update_quantity($cart_item_id, $user_id, $quantity);
    }
    
    public function remove_from_cart_ctr($cart_item_id, $user_id) {
        $cart = new Cart();
        return $cart->remove_from_cart($cart_item_id, $user_id);
    }
    
    public function clear_cart_ctr($user_id) {
        $cart = new Cart();
        return $cart->clear_cart($user_id);
    }
}

