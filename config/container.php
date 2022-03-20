<?php

use App\OAuth\Repositories\AccessTokenRepository;
use App\OAuth\Repositories\ClientRepository;
use App\OAuth\Repositories\RefreshTokenRepository;
use App\OAuth\Repositories\ScopeRepository;
use App\OAuth\Repositories\UserRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Views\Twig;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        return AppFactory::create();
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    'view' => function() {
        return Twig::create(__DIR__ . '/../resources/templates', ['cache' => false]);
    },

    'db' => function (ContainerInterface $container) {
        $capsule = new \Illuminate\Database\Capsule\Manager;
        $capsule->addConnection($container->get('settings')['db']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },

    RouteCollectorInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector();
    },
 
    'validator' => function () {
        return new Awurth\SlimValidation\Validator();
    },

    AuthorizationServer::class => function() {

        $server = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            'file://' . __DIR__ . '/../keys/private.key',
            getenv('SECRET_KEY')
        );

        $passwordGrant = new PasswordGrant(
            new UserRepository(),
            new RefreshTokenRepository()
        );
        $passwordGrant->setRefreshTokenTTL(
            new \DateInterval('P1M') // 1 month
        );

        $server->enableGrantType(
            $passwordGrant, new \DateInterval('PT1H')
        ); // 1 hour

        return $server;
    }
];