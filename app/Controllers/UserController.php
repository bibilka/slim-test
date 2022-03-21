<?php

namespace App\Controllers;

use App\Controllers\Base\ApiController;
use App\Models\User;
use App\OAuth\Repositories\UserRepository;
use Psr\Container\ContainerInterface;

final class UserController extends ApiController
{
    protected UserRepository $users;
    
    public function __construct(ContainerInterface $container){
        parent::__construct($container);
        $this->users = new UserRepository;
    }

    public function revokeTokens($request, $response, array $args)
    {
        $user = User::whereId($args['id'])->firstOrFail();
        $this->users->revokeAllTokens($user);
        return $this->jsonResponse($response, true, 'Токены пользователя ID='.$args['id'].' успешно аннулированы');
    }
}
