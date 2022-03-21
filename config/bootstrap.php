<?php

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

// формируем контейнер для приложения
$containerBuilder = new ContainerBuilder();

// подключаем параметры из файла .env
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/..//');
$dotenv->load();

// устанавливаем конфигурацию элементов контейнера
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

// Register database provider
$app->getContainer()->get('db');

// Register routes
(require __DIR__ . '/routes.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;