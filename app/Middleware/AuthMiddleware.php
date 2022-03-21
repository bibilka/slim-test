<?php

namespace App\Middleware;

use App\Exceptions\AuthErrorException;
use App\OAuth\Repositories\ClientRepository;
use App\OAuth\Repositories\ScopeRepository;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

class AuthMiddleware
{
    /**
     * @var ResourceServer
     */
    private $server;

    private App $app;

    private AuthorizationServer $auth;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {

        $this->auth = $container->get(AuthorizationServer::class);
        $this->server = $container->get(ResourceServer::class);
        $this->app = $container->get(App::class);
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandler $handler)
    {
        try {

            $this->validate($request);

        } catch (OAuthServerException $exception) {

            try {
                $this->tryRefreshToken();
            } catch (\Exception $ex) {
                throw new AuthErrorException($request, 'Отказано в доступе');
            }

            $this->validate($request);
        }

        $response = $handler->handle($request);
        
        return $response;
    }

    protected function validate(ServerRequestInterface &$request)
    {
        if (!isset($_COOKIE['access_token'])) {
            throw new OAuthServerException('Access token отсутствует', 401, 'auth');
        }
        $request = $request->withHeader('authorization', 'Bearer '. $_COOKIE['access_token']);
        $request = $this->server->validateAuthenticatedRequest($request);
    }

    protected function tryRefreshToken()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            throw new OAuthServerException('refresh token отсутствует', 401, 'auth');
        }

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
        $this->auth->respondToAccessTokenRequest($refreshTokenRequest, $refreshTokenResponse);

        $responseData = json_decode($refreshTokenResponse->getBody(), true);

        if (isset($responseData['access_token']) && isset($responseData['refresh_token'])) {
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