<?php

namespace App\Controllers;

use App\Controllers\Base\ApiController;
use App\Models\User;
use App\OAuth\Repositories\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

/**
 * Класс-контроллер для работы с пользователями системы.
 */
final class UserController extends ApiController
{
    /**
     * @var UserRepository $users Репозиторий для работы с пользователями
     */
    protected UserRepository $users;
    
    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->users = new UserRepository;
    }

    /**
     * Метод для аннулирования всех токенов заданного пользователя.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args Аргументы передаваемые в урле
     * 
     * @return Response
     */
    public function revokeTokens(ServerRequestInterface $request, ResponseInterface $response, array $args) : Response
    {
        $user = User::whereId($args['id'])->firstOrFail();
        $this->users->revokeAllTokens($user);
        return $this->jsonResponse($response, true, 'Токены пользователя ID='.$args['id'].' успешно аннулированы');
    }
}
