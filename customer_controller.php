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
    
    public function login_customer_ctr($email, $password) {
        $customer = new Customer();
        $user = $customer->get_customer_by_email($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'success' => true, 
                'message' => 'Login successful',
                'user' => $user
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'Invalid email or password'
            ];
        }
    }
    
    public function delete_customer_ctr($id) {
        $customer = new Customer();
        return $customer->delete($id);
    }
}
?>