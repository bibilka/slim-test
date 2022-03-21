<?php

if (!function_exists('dd')) {
    
    /**
     * Форматированный вывод переданных данных и остановка выполнения скрипта.
     * @param mixed $data
     * @return void
     */
    function dd($data) {
        echo '<pre>';
        die(print_r($data));
        echo '</pre>';
    }
}

if (!function_exists('isDebugMode')) {

    /**
     * @return bool Находится ли приложение в состоянии дебага (разработки).
     */
    function isDebugMode() {
        return filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
    }
}