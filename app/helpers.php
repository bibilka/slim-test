<?php

if (!function_exists('dd')) {
    function dd($data){
        echo '<pre>';
        die(var_dump($data));
        echo '</pre>';
    }
}