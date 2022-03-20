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
        $refreshToken->refresh_token = 'refresh_token_test'; // ???
        $refreshToken->identifier = $refreshTokenEntity->getIdentifier();
        $refreshToken->expired_at = $refreshTokenEntity->getExpiryDateTime();
        $refreshToken->save();
        $this->token = $refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        RefreshToken::whereIdentifier($tokenId)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return RefreshToken::whereIdentifier($tokenId)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}