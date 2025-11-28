<?php
require_once '../models/order_class.php';

class OrderController {
    
    /**
     * Create a new order
     * @param array $params Order parameters
     * @return array Result with order_id and order_reference
     */
    public function create_order_ctr($params) {
        $order = new Order();
        return $order->create_order($params);
    }
    
    /**
     * Add order details (product items) to an order
     * @param array $params Order detail parameters
     * @return array Result
     */
    public function add_order_details_ctr($params) {
        $order = new Order();
        return $order->add_order_details($params);
    }
    
    /**
     * Record a payment for an order
     * @param array $params Payment parameters
     * @return array Result
     */
    public function record_payment_ctr($params) {
        $order = new Order();
        return $order->record_payment($params);
    }
    
    /**
     * Get all orders for a customer
     * @param int $customer_id Customer ID
     * @return array|false Array of orders or false
     */
    public function get_customer_orders_ctr($customer_id) {
        $order = new Order();
        return $order->get_customer_orders($customer_id);
    }
    
    /**
     * Get order details with product information
     * @param int $order_id Order ID
     * @return array|false Array of order details or false
     */
    public function get_order_details_ctr($order_id) {
        $order = new Order();
        return $order->get_order_details($order_id);
    }
    
    /**
     * Get payment information for an order
     * @param int $order_id Order ID
     * @return array|false Payment data or false
     */
    public function get_order_payment_ctr($order_id) {
        $order = new Order();
        return $order->get_order_payment($order_id);
    }
    
    /**
     * Generate a unique order reference
     * @return string Order reference
     */
    public function generate_order_reference_ctr() {
        $order = new Order();
        return $order->generate_order_reference();
    }
    
    /**
     * Generate a unique transaction reference
     * @return string Transaction reference
     */
    public function generate_transaction_reference_ctr() {
        $order = new Order();
        return $order->generate_transaction_reference();
    }
}

