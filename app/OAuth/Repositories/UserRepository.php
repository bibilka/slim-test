<?php

namespace App\OAuth\Repositories;

use App\Models\User;
use App\OAuth\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $user = User::whereName($username)->first();

        if ($user && password_verify($password, $user->password)) {
            return new UserEntity($user);
        }

        return null;
    }
}