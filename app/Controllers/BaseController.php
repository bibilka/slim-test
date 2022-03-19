<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Views\Twig;
use Awurth\SlimValidation\Validator;
use Slim\Psr7\Response;

abstract class BaseController
{
    protected Twig $view;
    protected $db;

    protected array $routes;

    protected App $app;

    protected Validator $validator;

    public function __construct(ContainerInterface $container){
        $this->view = $container->get('view');
        $this->db = $container->get('db');

        $this->routes = [];

        $routes = $container->get(RouteCollectorInterface::class)->getRoutes();
        $parser = $container->get(RouteCollectorInterface::class)->getRouteParser();

        foreach ($routes as $route) {
            try {
                $this->routes[$route->getName()] = $parser->urlFor($route->getName());
            } catch (\Exception $ex) {}
            
        }

        $this->app = $container->get(App::class);

        $this->validator = $container->get('validator');
    }

    protected function jsonResponse(
        Response $response,
        bool $status,
        string $message = "",
        array $data = [],
        int $code = 200
    ): Response {
        $result = [
            'code' => $code,
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
        $payload = json_encode($result);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}