<?php

use App\Handlers\ErrorHandler;
use Slim\App;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    // // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    if (isDebugMode()) {
        $app->add(new Zeuxisoo\Whoops\Slim\WhoopsMiddleware);
    } else {
        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler(ErrorHandler::class);
    }
    
    // Create Twig
    $app->add(TwigMiddleware::createFromContainer($app));
};