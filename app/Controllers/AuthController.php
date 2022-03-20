<?php

namespace App\Controllers;

use App\Controllers\BaseController as Controller;
use App\Exceptions\AuthErrorException;
use App\Services\UserService;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для авторизации и регистрации пользователей.
 */
class AuthController extends Controller
{
    protected UserService $users;

    protected AuthorizationServer $auth;

    public function __construct(ContainerInterface $container){
        parent::__construct($container);
        $this->users = new UserService($container);
        $this->auth = $container->get(AuthorizationServer::class);
    }

    public function showLoginPage($request, $response)
    {
        return $this->view->render($response, 'login.twig', ['routes' => $this->routes]);
    }

    public function doAuth($request, $response)
    {
        try {
            return $this->auth->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $ex) {
            throw new AuthErrorException($request, 'Неверные имя пользователя или пароль.');
        }
        
    }

    public function showRegisterPage($request, $response)
    {
        return $this->view->render($response, 'register.twig', ['routes' => $this->routes]);
    }

    public function doSignUp(Request $request, $response)
    {
        $requestedData = $request->getParsedBody();

        $user = $this->users->registerNewUser($request->getParsedBody());

        return $this->jsonResponse($response, true, 'Регистрация прошла успешно', ['user_id' => $user->id]);
    }

    public function logout($request, $response)
    {
        // ... do logout
        return $response->withHeader('Location', $this->routes['loginPage'])->withStatus(302);
    }

    public function refreshToken($request, $response)
    {
        // ...
    }
}
    