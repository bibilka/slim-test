<?php

namespace App\Controllers\Traits;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;

trait RouteAccess
{
    protected array $routes;

    public function __construct(ContainerInterface $container)
    {
        $this->routes = [];

        $routes = $container->get(RouteCollectorInterface::class)->getRoutes();
        $parser = $container->get(RouteCollectorInterface::class)->getRouteParser();

        foreach ($routes as $route) {
            try {
                if ($route->getName()) {
                    $this->routes[$route->getName()] = $parser->urlFor($route->getName());
                }
                
            } catch (\Exception $ex) {}
            
        }
    }
}