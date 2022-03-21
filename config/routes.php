<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\ViewController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/app', function (RouteCollectorProxy $group) {
        $group->get('/profile', [ViewController::class, 'profile'])->setName('profilePage');
        $group->get('/home', [ViewController::class, 'home'])->setName('homePage');
    })->add(new AuthMiddleware($app->getContainer()));

    $app->redirect('/', '/app/home');

    $app->get('/login', [ViewController::class, 'login'])->setName('loginPage');
    $app->get('/register', [ViewController::class, 'register'])->setName('registerPage');

    $app->post('/auth/signup', [AuthController::class, 'signUp'])->setName('doSignUp');
    $app->post('/oauth/auth', [AuthController::class, 'signIn'])->setName('doSignIn');

    $app->get('/auth/logout', [AuthController::class, 'logout'])->setName('logout')->add(new AuthMiddleware($app->getContainer()));

    $app->post('/oauth/refresh_token', [AuthController::class, 'refreshToken'])->setName('refreshToken');
    $app->get('/users/{id}/revoke_tokens', [UserController::class, 'revokeTokens'])->setName('revokeTokensByUser');
};
