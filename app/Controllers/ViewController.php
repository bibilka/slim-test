<?php

namespace App\Controllers;

use App\Controllers\Traits\RouteAccess;
use App\OAuth\Repositories\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ViewController
{
    use RouteAccess {
        RouteAccess::__construct as private __routeAccess;
    }

    protected Twig $view;

    protected UserRepository $users;

    public function __construct(ContainerInterface $container)
    {
        $this->__routeAccess($container);

        $this->view = $container->get('view');

        $this->users = new UserRepository();
    }

    public function profile($request, $response)
    {
        return $this->showPage('profile', $request, $response);
    }

    public function home($request, $response)
    {
        return $this->showPage('home', $request, $response);
    }

    public function login($request, $response)
    {
        return $this->showPage('login', $request, $response);
    }

    public function register($request, $response)
    {
        return $this->showPage('register', $request, $response);
    }

    protected function showPage(string $page, ServerRequestInterface $request, ResponseInterface $response, array $data = []) 
    {
        return $this->view->render(
            $response, 
            "$page.twig", 
            array_merge($data, $this->getDefaultData($request))
        );
    }

    protected function getDefaultData(ServerRequestInterface $request)
    {
        return [
            'routes' => $this->routes,
            'user' => $this->users->getByCurrentAuth($request)?->toArray(),
            'oauth' => [
                'client_id' => getenv('OAUTH_CLIENT_ID'),
                'client_secret' => getenv('OAUTH_CLIENT_SECRET')
            ]
        ];
    }
}