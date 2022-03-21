<?php

namespace App\Middleware;

use App\Exceptions\AuthErrorException;
use App\OAuth\Repositories\ScopeRepository;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

/**
 * Middleware для выполнения валидации токенов доступа.
 */
class AuthMiddleware
{
    /**
     * @var ResourceServer Сервер ресурсов OAuth
     */
    private $resourceServer;

    /**
     * @var App Объект приложения.
     */
    private App $app;

    /**
     * @var AuthorizationServer Сервер авторизации OAuth
     */
    private AuthorizationServer $authServer;

    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->authServer = $container->get(AuthorizationServer::class);
        $this->resourceServer = $container->get(ResourceServer::class);
        $this->app = $container->get(App::class);
    }

    /**
     * Выполнение Middleware.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * 
     * @throws AuthErrorException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandler $handler)
    {
        try {
            // валидируем запрос
            $this->validate($request);

        } catch (OAuthServerException $exception) {

            try {
                // пытаемся получить новый токен, используя refresh токен
                $this->tryRefreshToken();
            } catch (\Exception $ex) {
                throw new AuthErrorException($request, 'Отказано в доступе');
            }

            // если новый токен удалось получить - снова валидируем запрос
            $this->validate($request);
        }

        $response = $handler->handle($request);
        
        return $response;
    }

    /**
     * Валидация запроса, который требует авторизации.
     * @param ServerRequestInterface $request
     * 
     * @throws OAuthServerException
     */
    protected function validate(ServerRequestInterface &$request)
    {
        /**
         * Так как по логике при использовании OAuth протокола, токенов и прочего -
         * со стороны фронтэнда напрашивается архитектура типа SPA (но таковой не имеется)
         * Исходя из того что задание является тестовым и целью является спрограммировать именно архитектуру серверной авториации -
         * было решение принебречь деталями обработки токенов на стороне клиентской части приложения и передавать токен после авторизации через куки
         * Но все равно перемещать его в Autorization header (ибо так бы оно работало при полностью идеальной архитекутре)
         */
        if (!isset($_COOKIE['access_token'])) {
            throw new OAuthServerException('Access token отсутствует', 401, 'auth');
        }
        $request = $request->withHeader('authorization', 'Bearer '. $_COOKIE['access_token']);

        $request = $this->resourceServer->validateAuthenticatedRequest($request);

        setcookie('user_id', $request->getAttribute('oauth_user_id'));
    }

    /**
     * Попытка выпустить новый токен доступа используя Refresh токен.
     * 
     * @throws OAuthServerException
     */
    protected function tryRefreshToken()
    {
        /**
         * Тут логика примерно та же, что описана в методе выше.
         */

        // пытаемся получить refresh токен
        if (!isset($_COOKIE['refresh_token'])) {
            throw new OAuthServerException('refresh token отсутствует', 401, 'auth');
        }

        // формируем имитацию реквеста, который был бы сформирован в случае реального запроса на end-point получения токена
        $refreshTokenRequest = (new ServerRequest('POST', '/oauth/auth', [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Accept' => 'application/json, text/javascript, */*; q=0.01'
        ]))->withCookieParams($_COOKIE)->withQueryParams($_GET)->withParsedBody([
            'grant_type' => 'refresh_token',
            'refresh_token' => $_COOKIE['refresh_token'],
            'client_id' => getenv('OAUTH_CLIENT_ID'),
            'client_secret' => getenv('OAUTH_CLIENT_SECRET'),
            'scope' => ScopeRepository::BASIC_SCOPE
        ]);
        $refreshTokenResponse = $this->app->getResponseFactory()->createResponse();

        // выполняем запрос сервер авторизации для получения нового токена
        $this->authServer->respondToAccessTokenRequest($refreshTokenRequest, $refreshTokenResponse);
        $responseData = json_decode($refreshTokenResponse->getBody(), true);

        if (isset($responseData['access_token']) && isset($responseData['refresh_token'])) {
            // сохраняем новые токены в куки
            foreach (['access_token', 'refresh_token'] as $tokenType) {
                setcookie($tokenType, $responseData[$tokenType], time() + $responseData['expires_in'], '/');
                if (isset($_COOKIE[$tokenType])) {
                    unset($_COOKIE[$tokenType]);
                }
                $_COOKIE[$tokenType] = $responseData[$tokenType];
            }
        }
    }
}