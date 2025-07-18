<?php

namespace wc_meta;

abstract class WC_Meta_Page {
    /**
     * Проверяет, активен ли обработчик на текущей странице
     * @return bool
     */
    abstract public function is_active();

    /**
     * Выводит мета-свойства в <head>
     * @return void
     */
    abstract public function output_meta();
} 