<?php
/**
 * Класс для реализации JavaScript функций работы с корзиной WooCommerce
 */
namespace wc_meta;

class WC_JS_Cart_Funcs {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_add_to_cart', [$this, 'ajax_add_to_cart']);
        add_action('wp_ajax_nopriv_add_to_cart', [$this, 'ajax_add_to_cart']);
        add_action('wp_ajax_remove_from_cart', [$this, 'ajax_remove_from_cart']);
        add_action('wp_ajax_nopriv_remove_from_cart', [$this, 'ajax_remove_from_cart']);
        add_action('wp_ajax_get_cart_state', [$this, 'ajax_get_cart_state']);
        add_action('wp_ajax_nopriv_get_cart_state', [$this, 'ajax_get_cart_state']);
    }

    /**
     * Подключает скрипты и стили
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wc-js-cart-funcs',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/cart-funcs.js',
            ['jquery'],
            '1.1.0',
            true
        );

        // Локализация для AJAX
        wp_localize_script('wc-js-cart-funcs', 'wcCartAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wc_cart_nonce'),
            'debug' => defined('WP_DEBUG') && WP_DEBUG,
        ]);
    }

    /**
     * AJAX обработчик добавления товара в корзину
     */
    public function ajax_add_to_cart() {
        check_ajax_referer('wc_cart_nonce', 'nonce');

        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);

        if (!$product_id) {
            wp_send_json_error('Неверный ID товара');
            return;
        }

        // Проверяем существование товара
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error('Товар не найден');
            return;
        }

        // Добавляем товар в корзину
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);

        if ($cart_item_key) {
            wp_send_json_success([
                'message' => 'Товар добавлен в корзину',
                'cart_item_key' => $cart_item_key
            ]);
        } else {
            wp_send_json_error('Ошибка добавления товара в корзину');
        }
    }

    /**
     * AJAX обработчик удаления товара из корзины
     */
    public function ajax_remove_from_cart() {
        check_ajax_referer('wc_cart_nonce', 'nonce');

        $product_id = intval($_POST['product_id'] ?? 0);

        if (!$product_id) {
            wp_send_json_error('Неверный ID товара');
            return;
        }

        // Находим товар в корзине и удаляем его
        $cart_items = WC()->cart->get_cart();
        $removed = false;

        foreach ($cart_items as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                WC()->cart->remove_cart_item($cart_item_key);
                $removed = true;
                break;
            }
        }

        if ($removed) {
            wp_send_json_success([
                'message' => 'Товар удален из корзины'
            ]);
        } else {
            wp_send_json_error('Товар не найден в корзине');
        }
    }

    /**
     * AJAX обработчик получения состояния корзины
     */
    public function ajax_get_cart_state() {
        check_ajax_referer('wc_cart_nonce', 'nonce');

        $cart_items = WC()->cart->get_cart();
        $cart_state = [];

        foreach ($cart_items as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];
            
            if (isset($cart_state[$product_id])) {
                $cart_state[$product_id] += $quantity;
            } else {
                $cart_state[$product_id] = $quantity;
            }
        }

        wp_send_json_success($cart_state);
    }
}
