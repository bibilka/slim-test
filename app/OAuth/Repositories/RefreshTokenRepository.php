<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\Models\RefreshToken;
use App\OAuth\Entities\RefreshTokenEntity;
use Carbon\Carbon;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * Класс-репозиторий для работы с OAuth сущностью Refresh Token (токенами обновления).
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * Сохранение рефреш токена в базу данных.
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshToken = new RefreshToken();
        $refreshToken->access_token_id = AccessToken::whereIdentifier($refreshTokenEntity->getAccessToken()->getIdentifier())->firstOrFail()->id;
        $refreshToken->issued_at = Carbon::now();
        $refreshToken->expired_at = $refreshTokenEntity->getExpiryDateTime();
        $refreshToken->identifier = $refreshTokenEntity->getIdentifier();
        $refreshToken->save();
    }

    /**
     * Аннулировать refresh token.
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        $token = RefreshToken::whereIdentifier($tokenId)->first();
        if (!$token)
            return;
        $token->revoke();
    }

    /**
     * Проверить является ли refresh токен аннулированным.
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $token = RefreshToken::whereIdentifier($tokenId)->first();

        if ($token === null || $token->isRevoked()) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}