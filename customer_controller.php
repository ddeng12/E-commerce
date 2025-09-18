<?php
require_once 'customer_class.php';

class CustomerController {
    
    public function register_customer_ctr($args) {
        $customer = new Customer();
        return $customer->add($args);
    }
    
    public function edit_customer_ctr($id, $args) {
        $customer = new Customer();
        return $customer->edit($id, $args);
    }
    
    public function delete_customer_ctr($id) {
        $customer = new Customer();
        return $customer->delete($id);
    }
}
?>