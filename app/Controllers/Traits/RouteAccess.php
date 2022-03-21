<?php

namespace App\Controllers\Traits;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;

/**
 * Трейт, который предоставляет контроллерам возможность получить список роутов, которые заведены в приложении.
 */
trait RouteAccess
{
    /**
     * @var array $routes Массив, который представляет собой список роутов [routeName => url]
     */
    protected array $routes;

    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
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