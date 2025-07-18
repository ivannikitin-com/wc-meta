<?php
/**
 * Обработчик мета-свойств для страницы "Спасибо за заказ"
 */
namespace wc_meta;

class WC_Meta_ThankYou extends WC_Meta_Page {
    public function is_active() {
        // Проверяем, страница ли это "Спасибо за заказ"
        return function_exists('is_order_received_page') && is_order_received_page();
    }

    public function output_meta() {
        if (!function_exists('wc_get_order')) {
            return;
        }
        // Используем order-received, если есть, иначе order
        $order_id = isset($_GET['order-received']) ? intval($_GET['order-received']) : (isset($_GET['order']) ? intval($_GET['order']) : 0);
        if (!$order_id) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        $status = $order->get_status();
        $total = $order->get_total();
        $customer_id = $order->get_customer_id();
        echo '<meta name="wc-order-status" content="' . esc_attr($status) . '" />';
        echo '<meta name="wc-order-total" content="' . esc_attr($total) . '" />';
        if ($customer_id) {
            echo '<meta name="wc-customer-id" content="' . esc_attr($customer_id) . '" />';
        }
    }
} 