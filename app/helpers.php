<?php

if (!function_exists('dd')) {
    function dd($data){
        echo '<pre>';
        die(print_r($data));
        echo '</pre>';
    }
}

if (!function_exists('isDebugMode')) {
    function isDebugMode(){
        return filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
    }
}