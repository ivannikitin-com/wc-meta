<?php
/**
 * Класс плагина
 */
namespace wc_meta;

class Plugin {
    /**
     * Обработчики мета-свойств
     * @var array
     */ 
    protected $handlers = [];

    // Конструктор
    public function __construct() {
        $this->handlers[] = new WC_Meta_ThankYou();
        add_action('wp_head', [$this, 'handle']);
        add_action( 'init', [ $this, 'init' ] );
    }

    // Инициализация компонентов плагина и хуки
    public function init() {
        // Можно добавить инициализацию других компонентов
    }

    public function handle() {
        foreach ($this->handlers as $handler) {
            if ($handler->is_active()) {
                $handler->output_meta();
                break;
            }
        }
    }
}
