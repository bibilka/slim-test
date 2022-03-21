<?php

namespace App\OAuth\Entities;

use App\Models\User;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * OAuth сущность - User (Пользователь системы).
 */
class UserEntity implements UserEntityInterface
{
    /**
     * @var User $user Объект пользователя для работы с базой данных.
     */
    protected User $user;

    /**
     * Инициализация.
     * @param User $user Объект пользователя для работы с базой данных.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Return the user's identifier.
     *
     * @return int
     */
    public function getIdentifier() : int
    {
        return $this->user->id;
    }
}