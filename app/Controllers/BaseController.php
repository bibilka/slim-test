<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Views\Twig;

abstract class BaseController
{
    protected Twig $view;
    protected $db;

    protected array $routes;

    protected App $app;

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
    }
}