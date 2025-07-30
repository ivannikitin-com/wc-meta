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
        
        // Получаем order_id
        $order_id = get_query_var('order-received');
        \WP_DEBUG && error_log('WC_Meta_ThankYou: order_id get_query_var: #' . $order_id);
        
        // Сначала пробуем получить из параметров (для обратной совместимости)
        if ( !$order_id && isset($_GET['order-received'])) {
            $order_id = intval($_GET['order-received']);
        } elseif ( !$order_id && isset($_GET['order'])) {
            $order_id = intval($_GET['order']);
        }
        
        // Если не получили из параметров, извлекаем из URL
        if (!$order_id) {
            $current_url = $_SERVER['REQUEST_URI'] ?? '';
            // Регулярное выражение для извлечения номера заказа из URL
            // Паттерн: /checkout/order-received/ЧИСЛО/
            if (preg_match('/\/checkout\/order-received\/(\d+)/', $current_url, $matches)) {
                $order_id = intval($matches[1]);
            }
        }
        
        if (!$order_id) {
            \WP_DEBUG && error_log('WC_Meta_ThankYou: order_id not found');
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order) {
            \WP_DEBUG && error_log('WC_Meta_ThankYou: order #' . $order_id . ' not found');
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
