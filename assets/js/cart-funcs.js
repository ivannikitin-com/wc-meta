/**
 * JavaScript функции для работы с корзиной WooCommerce
 * Реализует window.digiLayer объект с методами addToCart, removeFromCart и cartState
 */

(function() {
    'use strict';

    // Создаем глобальный объект digiLayer, если он не существует
    if (typeof window.digiLayer === 'undefined') {
        window.digiLayer = {};
    }

    /**
     * Добавление товара в корзину
     * @param {number} productId - ID товара
     * @param {number} quantity - Количество (по умолчанию 1)
     * @returns {Promise} Promise с результатом операции
     */
    window.digiLayer.addToCart = function(productId, quantity = 1) {
        return new Promise(function(resolve, reject) {
            // Логирование в режиме отладки
            if (window.wcCartAjax && window.wcCartAjax.debug) {
                console.log(`[WC Cart Debug] addToCart called with productId: ${productId}, quantity: ${quantity}`);
            }

            if (!productId || productId <= 0) {
                reject(new Error('Неверный ID товара'));
                return;
            }

            if (!window.wcCartAjax) {
                reject(new Error('WC Cart AJAX не инициализирован'));
                return;
            }

            jQuery.ajax({
                url: window.wcCartAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: window.wcCartAjax.nonce
                },
                success: function(response) {
                    // Логирование в режиме отладки
                    if (window.wcCartAjax && window.wcCartAjax.debug) {
                        console.log('[WC Cart Debug] addToCart response:', response);
                    }

                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(new Error(response.data || 'Ошибка добавления товара'));
                    }
                },
                error: function(xhr, status, error) {
                    reject(new Error('Ошибка AJAX: ' + error));
                }
            });
        });
    };

    /**
     * Удаление товара из корзины
     * @param {number} productId - ID товара
     * @returns {Promise} Promise с результатом операции
     */
    window.digiLayer.removeFromCart = function(productId) {
        return new Promise(function(resolve, reject) {
            // Логирование в режиме отладки
            if (window.wcCartAjax && window.wcCartAjax.debug) {
                console.log(`[WC Cart Debug] removeFromCart called with productId: ${productId}`);
            }

            if (!productId || productId <= 0) {
                reject(new Error('Неверный ID товара'));
                return;
            }

            if (!window.wcCartAjax) {
                reject(new Error('WC Cart AJAX не инициализирован'));
                return;
            }

            jQuery.ajax({
                url: window.wcCartAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'remove_from_cart',
                    product_id: productId,
                    nonce: window.wcCartAjax.nonce
                },
                success: function(response) {
                    // Логирование в режиме отладки
                    if (window.wcCartAjax && window.wcCartAjax.debug) {
                        console.log('[WC Cart Debug] removeFromCart response:', response);
                    }

                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(new Error(response.data || 'Ошибка удаления товара'));
                    }
                },
                error: function(xhr, status, error) {
                    reject(new Error('Ошибка AJAX: ' + error));
                }
            });
        });
    };

    /**
     * Получение текущего состояния корзины
     * @returns {Promise} Promise с объектом состояния корзины
     */
    window.digiLayer.cartState = function() {
        return new Promise(function(resolve, reject) {
            // Логирование в режиме отладки
            if (window.wcCartAjax && window.wcCartAjax.debug) {
                console.log('[WC Cart Debug] cartState called');
            }

            if (!window.wcCartAjax) {
                reject(new Error('WC Cart AJAX не инициализирован'));
                return;
            }

            jQuery.ajax({
                url: window.wcCartAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_cart_state',
                    nonce: window.wcCartAjax.nonce
                },
                success: function(response) {
                    // Логирование в режиме отладки
                    if (window.wcCartAjax && window.wcCartAjax.debug) {
                        console.log('[WC Cart Debug] cartState response:', response);
                    }

                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(new Error(response.data || 'Ошибка получения состояния корзины'));
                    }
                },
                error: function(xhr, status, error) {
                    reject(new Error('Ошибка AJAX: ' + error));
                }
            });
        });
    };

    // Логирование инициализации только в режиме отладки
    if (window.wcCartAjax && window.wcCartAjax.debug) {
        console.log('WC Cart Functions initialized. Available methods:');
        console.log('- window.digiLayer.addToCart(productId, quantity)');
        console.log('- window.digiLayer.removeFromCart(productId)');
        console.log('- window.digiLayer.cartState()');
    }

})();