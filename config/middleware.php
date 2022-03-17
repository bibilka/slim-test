<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    // // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);

    // Create Twig
    $app->add(TwigMiddleware::createFromContainer($app));
};