<?php

use App\Handlers\ErrorHandler;
use Slim\App;
use Slim\Views\TwigMiddleware;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    // // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    // $app->add(WhoopsMiddleware::class);
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler(ErrorHandler::class);
    
    // Create Twig
    $app->add(TwigMiddleware::createFromContainer($app));
};