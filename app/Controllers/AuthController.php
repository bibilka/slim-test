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
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

/**
 * Контроллер для авторизации и регистрации пользователей.
 */
final class AuthController extends ApiController
{
    use RouteAccess {
        RouteAccess::__construct as private __routeAccess;
    }

    /**
     * @var UserService $service Сервис для работы с пользователями.
     */
    protected UserService $service;

    /**
     * @var UserRepository $users Репозиторий для работы с пользователями.
     */
    protected UserRepository $users;

    /**
     * @var AuthorizationServer $auth Сервер авторизации OAuth 2.0
     */
    protected AuthorizationServer $auth;

    /**
     * @var AccessTokenRepository $tokens Репозиторий для работы с токенами.
     */
    protected AccessTokenRepository $tokens;

    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        // конструктор апи-контроллера
        parent::__construct($container);

        // подключаем доступ к массиву роутов
        $this->__routeAccess($container);
        
        // инициализируем сервисы и репозитории необходимые для работы с авторизацией
        $this->service = new UserService($container);
        $this->auth = $container->get(AuthorizationServer::class);

        $this->tokens = new AccessTokenRepository();
        $this->users = new UserRepository();
    }

    /**
     * Метод для выполнения авторизации пользователей.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @throws \App\Exceptions\AuthErrorException
     * 
     * @return ResponseInterface
     */
    public function signIn(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        try {
            // валидируем переданные данные
            // Результатом успешной авторизации является получение access и refresh токенов.
            return $this->auth->respondToAccessTokenRequest($request, $response);

        } catch (OAuthServerException $ex) {

            // иначе возвращаем ошибку
            throw new AuthErrorException($request, 'Неверные имя пользователя или пароль.');
        }
    }

    /**
     * Метод для выполнения регистрации пользователей.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @throws \Respect\Validation\Exceptions\ValidationException
     * 
     * @return Response
     */
    public function signUp(ServerRequestInterface $request, ResponseInterface $response) : Response
    {
        // валидируем данные и в случае успеха регистрируем нового пользователя
        $user = $this->service->registerNewUser(
            $request->getParsedBody()
        );

        return $this->jsonResponse($response, true, 'Регистрация прошла успешно', ['user_id' => $user->id]);
    }

    /**
     * Метод для деавторизации текущего авторизованного пользователя.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function logout(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        // тут спорный момент, что в текущей архитектуре подразумевать под "logout"

        // либо можно отозвать текущий активный токен, под которым прошла последняя авторизация
        $this->tokens->revokeCurrentToken($request);

        // либо можно отозвать вообще все токены текущего пользователя
        $this->users->deauthorizeCurrentUser($request);

        // еще тут по идеи можно чистить куки, сессию и прочее...
        // ...

        // редирект на страницу входа
        return $response->withHeader('Location', $this->routes['loginPage'])->withStatus(302);
    }

    /**
     * Метод для получения нового токена доступа (access_token) используя раннее полученный refresh_token.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @throws \App\Exceptions\BaseAppException
     * 
     * @return ResponseInterface
     */
    public function refreshToken(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        try {
            // здесь снова обращаемся к тому же методу авторизационного сервера, что и при получении токена по логину и паролю
            // подразумевается что при обращении к этому end-point данные в запросе будут соотвественно под получение refresh_token
            return $this->auth->respondToAccessTokenRequest($request, $response);
            
        } catch (\Exception $ex) {
            throw new BaseAppException($request, $ex->getMessage(), $ex->getCode());
        }
        // ...
        // теоретически можно было бы добавить валидацию реквеста на наличие необходимых параметров
    }
}
    