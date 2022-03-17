<?php

use App\Controllers\HomeController;
use Slim\App;

return function (App $app) {
    $app->get('/', function($request,$response){
        return $this->get('view')->render($response, 'home.twig', ['test' => 'test123']);
    });

    $app->get('/home/profile', [HomeController::class, 'profile']);
};
