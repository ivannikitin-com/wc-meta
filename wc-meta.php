<?php

/**
 * Plugin Name: WC Meta
 * Description: Вывод ряда свойств объектов WooCommerce в виде мета-свойств страницы.
 *
 * Plugin URI:  Ссылка на страницу плагина
 * Author URI:  https://ivannikitin.com
 * Author:      Иван Никитин и партнеры
 *
 * Text Domain: wc-meta
 * Domain Path: /languages
 *
 * Requires at least: 5.7
 * Requires PHP: 7.4
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html

 *
 * Requires Plugins: woocommerce
 *
 * Version:     1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Файлы плагина
require_once __DIR__ . '/includes/class-wc-meta-page.php';
require_once __DIR__ . '/includes/class-wc-meta-thankyou.php';
require_once __DIR__ . '/includes/class-wc-js-cart-funcs.php';
require_once __DIR__ . '/includes/class-plugin.php';

// Запуск плагина
new wc_meta\Plugin();
