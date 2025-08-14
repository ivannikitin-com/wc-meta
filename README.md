# wc-meta

Плагин WordPress для вывода ряда свойств объектов WooCOmmerce в виде мета-свойств страницы.
Необходим для интеграции с рядом служб, например, с anyQuery.

## Требования

- WordPress 5.7 или выше
- PHP 7.4 или выше
- WooCommerce (активный)

## Установка

1. Скопируйте папку плагина в директорию `wp-content/plugins/` вашего сайта WordPress.
2. Активируйте плагин через админку WordPress (раздел «Плагины»).
3. Убедитесь, что WooCommerce активен.

## Основные функции и архитектура

### Мета-свойства страниц

- **wc_meta\\Plugin** — основной класс плагина. Инициализирует обработчики мета-свойств и регистрирует их вывод в `<head>` через хук `wp_head`.
- **wc_meta\\WC_Meta_Page** — абстрактный базовый класс для всех обработчиков мета-свойств. Определяет интерфейс:
  - `is_active()` — определяет, активен ли обработчик на текущей странице.
  - `output_meta()` — выводит нужные мета-свойства в `<head>`.
- **wc_meta\\WC_Meta_ThankYou** — обработчик для страницы "Спасибо за заказ". Выводит мета-свойства заказа: статус, сумму и ID покупателя (если есть).

### JavaScript функции корзины

- **wc_meta\\WC_JS_Cart_Funcs** — класс для реализации JavaScript функций работы с корзиной WooCommerce. Предоставляет глобальный объект `window.digiLayer` с методами:
  - `addToCart(productId, quantity)` — добавление товара в корзину
  - `removeFromCart(productId)` — удаление товара из корзины  
  - `cartState()` — получение текущего состояния корзины

Все функции возвращают Promise и работают через AJAX запросы к WordPress.

**Режим отладки:** Если в WordPress включена константа `WP_DEBUG`, то в консоли браузера будет выводиться дополнительная отладочная информация о вызовах функций и ответах сервера.

### Как работает

1. При инициализации плагина создаётся объект `Plugin`, который регистрирует обработчики (например, `WC_Meta_ThankYou`).
2. На каждом рендере страницы вызывается метод `handle()`, который определяет, какой обработчик активен, и выводит соответствующие мета-свойства в `<head>`.
3. Для добавления новых типов страниц создайте класс-наследник от `WC_Meta_Page` и добавьте его в массив `$handlers` в классе `Plugin`.

## Примеры использования JavaScript функций

### Добавление товара в корзину
```javascript
window.digiLayer.addToCart(32761, 2)
    .then(result => {
        console.log('Товар добавлен:', result);
    })
    .catch(error => {
        console.error('Ошибка:', error);
    });
```

### Удаление товара из корзины
```javascript
window.digiLayer.removeFromCart(32761)
    .then(result => {
        console.log('Товар удален:', result);
    })
    .catch(error => {
        console.error('Ошибка:', error);
    });
```

### Получение состояния корзины
```javascript
window.digiLayer.cartState()
    .then(state => {
        console.log('Состояние корзины:', state);
        // Результат: {32761: 1, 32762: 2} - товар ID: количество
    })
    .catch(error => {
        console.error('Ошибка:', error);
    });
```

### Использование async/await
```javascript
async function manageCart() {
    try {
        // Добавляем товар
        await window.digiLayer.addToCart(32761);
        
        // Проверяем состояние
        const state = await window.digiLayer.cartState();
        console.log('Корзина после добавления:', state);
        
        // Удаляем товар
        await window.digiLayer.removeFromCart(32761);
        
        // Проверяем финальное состояние
        const finalState = await window.digiLayer.cartState();
        console.log('Корзина после удаления:', finalState);
    } catch (error) {
        console.error('Ошибка:', error);
    }
}
```

### Отладочная информация

При включенном `WP_DEBUG` в консоли браузера вы увидите:

```
[WC Cart Debug] addToCart called with productId: 32761, quantity: 1
[WC Cart Debug] addToCart response: {success: true, data: {...}}
[WC Cart Debug] cartState called
[WC Cart Debug] cartState response: {success: true, data: {32761: 1}}
```

Это поможет при разработке и отладке интеграций.
