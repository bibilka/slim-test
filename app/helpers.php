<?php

if (!function_exists('dd')) {
    function dd($data){
        echo '<pre>';
        die(print_r($data));
        echo '</pre>';
    }
}