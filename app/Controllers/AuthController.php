<?php

namespace App\Controllers;

use App\Controllers\Base\ApiController;
use App\Controllers\Traits\RouteAccess;
use App\Exceptions\AuthErrorException;
use App\Exceptions\BaseAppException;
use App\OAuth\Repositories\AccessTokenRepository;
use App\OAuth\Repositories\UserRepository;
use App\Services\UserService;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Контроллер для авторизации и регистрации пользователей.
 */
final class AuthController extends ApiController
{
    use RouteAccess {
        RouteAccess::__construct as private __routeAccess;
    }

    protected UserService $service;

    protected UserRepository $users;

    protected AuthorizationServer $auth;

    protected AccessTokenRepository $tokens;

    public function __construct(ContainerInterface $container) {

        parent::__construct($container);

        $this->__routeAccess($container);
        
        $this->service = new UserService($container);
        $this->auth = $container->get(AuthorizationServer::class);

        $this->tokens = new AccessTokenRepository();
        $this->users = new UserRepository();
    }

    public function signIn($request, ResponseInterface $response)
    {
        try {
            return $this->auth->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $ex) {
            throw new AuthErrorException($request, 'Неверные имя пользователя или пароль.');
        }
    }

    public function signUp(Request $request, $response)
    {
        $requestedData = $request->getParsedBody();

        $user = $this->service->registerNewUser($requestedData);

        return $this->jsonResponse($response, true, 'Регистрация прошла успешно', ['user_id' => $user->id]);
    }

    public function logout(ServerRequestInterface $request, $response)
    {
        // тут спорный момент, что в текущей архитектуре подразумевать под "logout"

        // либо можно отозвать текущий активный токен, под которым прошла последняя авторизация
        $this->tokens->revokeCurrentToken($request);

        // либо можно отозвать вообще все токены текущего пользователя
        $this->users->revokeAllTokens(
            $this->users->getByCurrentAuth($request)
        );

        return $response->withHeader('Location', $this->routes['loginPage'])->withStatus(302);
    }

    public function refreshToken($request, $response)
    {
        try {
            return $this->auth->respondToAccessTokenRequest($request, $response);
        } catch (\Exception $ex) {
            throw new BaseAppException($request, $ex->getMessage(), $ex->getCode());
        }
    }
}
    