<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\Models\RefreshToken;
use App\Models\User;
use App\OAuth\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Класс-репозиторий для работы с OAuth сущностью User (Пользователь системы).
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Проверка существования пользователя с заданным именем и валидация предлагаемого пароля.
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

    /**
     * Возвращает текущего авторизованного пользователя, если такой есть.
     * @return User|null
     */
    public function getByCurrentAuth(ServerRequestInterface $request) : ?User
    {
        return User::whereId($request->getAttribute('oauth_user_id'))->first();
    }

    /**
     * Аннулирует все существующие токены заданного пользователя.
     * @param User $user
     */
    public function revokeAllTokens(User $user)
    {
        $query = AccessToken::whereUserId($user->id);

        $query->update(['revoked' => true]);

        RefreshToken::whereIn('access_token_id', $query->get()->pluck('id'))->update(['revoked' => true]);
    }
}