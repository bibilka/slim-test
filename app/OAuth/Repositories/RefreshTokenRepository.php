<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\Models\RefreshToken;
use App\OAuth\Entities\RefreshTokenEntity;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $refreshToken = new RefreshToken();
        $refreshToken->access_token_id = AccessToken::whereIdentifier($refreshTokenEntity->getAccessToken()->getIdentifier())->firstOrFail()->id;
        $refreshToken->expired_at = $refreshTokenEntity->getExpiryDateTime();
        $refreshToken->identifier = $refreshTokenEntity->getIdentifier();
        $refreshToken->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        $token = RefreshToken::whereIdentifier($tokenId)->get();
        if (!$token)
            return;
        $token->revoke();
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $token = RefreshToken::whereIdentifier($tokenId)->get();
        if ($token === null || $token->isRevoked()) {
            return true;
        }
        return (new AccessTokenRepository())->isAccessTokenRevoked($token->access_token_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}