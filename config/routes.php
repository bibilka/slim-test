<?php

use App\Controllers\AppController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    $app->group('/app', function (RouteCollectorProxy $group) {
        $group->get('/profile', [AppController::class, 'showProfilePage'])->setName('profilePage');
        $group->get('/home', [AppController::class, 'showHomePage'])->setName('homePage');
    });

    $app->get('/login', [AuthController::class, 'showLoginPage'])->setName('loginPage');
    $app->get('/register', [AuthController::class, 'showRegisterPage'])->setName('registerPage');

    $app->post('/auth/signup', [AuthController::class, 'doSignUp'])->setName('doSignup');
    $app->post('/oauth/auth', [AuthController::class, 'doAuth'])->setName('doAuth');

    $app->get('/auth/logout', [AuthController::class, 'logout'])->setName('logout');

    $app->post('/auth/refresh_token', [AuthController::class, 'refreshToken'])->setName('refreshToken');
    $app->get('/users/{id}/clear_tokens', [UserController::class, 'clearAllTokens'])->setName('clearUsersTokens');
};
