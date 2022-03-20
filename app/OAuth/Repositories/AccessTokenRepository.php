<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\OAuth\Entities\AccessTokenEntity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $token = new AccessToken();
        $token->user_id = $accessTokenEntity->getUserIdentifier();
        $token->expired_at = $accessTokenEntity->getExpiryDateTime();
        $token->identifier = $accessTokenEntity->getIdentifier();
        $token->save();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $token = AccessToken::whereIdentifier($tokenId)->get();
        if (!$token)
            return;
        $token->revoke();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $token = AccessToken::whereIdentifier($tokenId)->get();
        if (!$token)
            return true;

        return $token->isRevoked();
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new AccessTokenEntity($userIdentifier, $scopes);
        $token->setClient($clientEntity);
        return $token;
    }
}