<?php

namespace App\Controllers;

use App\Controllers\Traits\RouteAccess;
use App\OAuth\Repositories\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Symfony\Component\Yaml\Yaml;

/**
 * Контроллер, отвечающий за рендеринг страниц веб-приложения.
 */
class ViewController
{
    use RouteAccess {
        RouteAccess::__construct as private __routeAccess;
    }

    /**
     * @var Twig $view Объект для рендера страниц через шаблонизатор twig
     */
    protected Twig $view;

    /**
     * @var UserRepository Репозиторий для работы с пользователями.
     */
    protected UserRepository $users;

    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // подключаем список роутов
        $this->__routeAccess($container);

        $this->view = $container->get('view');

        $this->users = new UserRepository();
    }

    /**
     * Отображение страницы профиля пользователя.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function profile(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->showPage('profile', $request, $response);
    }

    /**
     * Отображение домашней страницы.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function home(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->showPage('home', $request, $response);
    }

    /**
     * Отображение страницы для авторизации.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function login(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->showPage('login', $request, $response);
    }

    /**
     * Отображение страницы для регистрации.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function register(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->showPage('register', $request, $response);
    }

    /**
     * Отображение страницы с документацией к апи.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function swagger(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->showPage('swagger', $request, $response, [
            'spec' =>json_encode(Yaml::parseFile(__DIR__ . '/../../resources/docs/swagger.yaml')),
        ]);
    }

    /**
     * Метод для отображения (рендеринга) заданного шаблона через Twig.
     * 
     * @param string $page Название страницы (шаблона)
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $data Данные для передачи в шаблон
     * 
     * @return ResponseInterface
     */
    protected function showPage(string $page, ServerRequestInterface $request, ResponseInterface $response, array $data = []) : ResponseInterface
    {
        return $this->view->render(
            $response, 
            "$page.twig", 
            array_merge($data, $this->getDefaultData($request))
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @return array Данные передаваемые в шаблон по умолчанию
     */
    protected function getDefaultData(ServerRequestInterface $request) : array
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