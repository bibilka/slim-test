<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\Models\RefreshToken;
use App\Models\User;
use App\OAuth\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

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

    /**
     * @return User|null
     */
    public function getByCurrentAuth(ServerRequestInterface $request) : ?User
    {
        return User::whereId($request->getAttribute('oauth_user_id'))->first();
    }

    public function revokeAllTokens(User $user)
    {
        $query = AccessToken::whereUserId($user->id);

        $query->update(['revoked' => true]);

        RefreshToken::whereIn('access_token_id', $query->get()->pluck('id'))->update(['revoked' => true]);
    }
}