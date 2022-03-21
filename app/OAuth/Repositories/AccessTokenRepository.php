<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\OAuth\Entities\AccessTokenEntity;
use Carbon\Carbon;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Класс-репозиторий для работы с OAuth сущностью Access Token (токенами доступа).
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * Сохранение нового токена в базу данных.
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $token = new AccessToken();
        $token->user_id = $accessTokenEntity->getUserIdentifier();
        $token->issued_at = Carbon::now();
        $token->expired_at = $accessTokenEntity->getExpiryDateTime();
        $token->identifier = $accessTokenEntity->getIdentifier();
        $token->save();
    }

    /**
     * Аннулирование токена доступа.
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $token = AccessToken::whereIdentifier($tokenId)->first();
        if (!$token)
            return;
        
        $token->revoke();
    }

    /**
     * Аннулирование текущего активного токена доступа.
     * @param ServerRequestInterface $request
     */
    public function revokeCurrentToken(ServerRequestInterface $request)
    {
        $this->revokeAccessToken($request->getAttribute('oauth_access_token_id'));
    }

    /**
     * Проверить, является ли токен аннулированным.
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $token = AccessToken::whereIdentifier($tokenId)->first();
        if (!$token)
            return true;

        return $token->isRevoked();
    }

    /**
     * Создание нового токена доступа.
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new AccessTokenEntity($userIdentifier, $scopes);
        $token->setClient($clientEntity);
        return $token;
    }
}