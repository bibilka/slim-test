<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\ViewController;
use App\Middleware\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {

    // роуты, требующие авторизацию (профиль и домашняя страница)
    $app->group('/app', function (RouteCollectorProxy $group) {
        $group->get('/profile', [ViewController::class, 'profile'])->setName('profilePage');
        $group->get('/home', [ViewController::class, 'home'])->setName('homePage');
    })->add(new AuthMiddleware($app->getContainer()));

    // делаем редирект по умолчанию на домашнюю страницу
    $app->redirect('/', '/app/home');

    // роуты для страниц регистрации и авторизации
    $app->get('/login', [ViewController::class, 'login'])->setName('loginPage');
    $app->get('/register', [ViewController::class, 'register'])->setName('registerPage');

    // роут для выполнения POST запроса на локальное апи для регистрации нового пользователя в системе
    $app->post('/auth/signup', [AuthController::class, 'signUp'])->setName('doSignUp');
    // роут для выполнения POST запроса на локальное апи с целью получения токена доступа
    $app->post('/oauth/auth', [AuthController::class, 'signIn'])->setName('doSignIn');

    // роут для деавторизации текущего активного пользователя
    $app->get('/auth/logout', [AuthController::class, 'logout'])->setName('logout')->add(new AuthMiddleware($app->getContainer()));

    // роут для выполнения POST запроса на локальное апи для получение нового токена доступа
    $app->post('/oauth/refresh_token', [AuthController::class, 'refreshToken'])->setName('refreshToken');
    // роут для выполнения POST запроса на локальное апи, чтобы анулировать все существующие токены для заданного пользователя
    $app->post('/users/{id}/revoke_tokens', [UserController::class, 'revokeTokens'])->setName('revokeTokensByUser');

    // роут отображает страницу со свагером (документация к api)
    $app->get('/docs/api/v1', [ViewController::class, 'swagger'])->setName('swagger');
};
