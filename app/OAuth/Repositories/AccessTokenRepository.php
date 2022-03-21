<?php

namespace App\OAuth\Repositories;

use App\Models\AccessToken;
use App\OAuth\Entities\AccessTokenEntity;
use Carbon\Carbon;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
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
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        $token = AccessToken::whereIdentifier($tokenId)->first();
        if (!$token)
            return;
        
        $token->revoke();
    }

    public function revokeCurrentToken(ServerRequestInterface $request)
    {
        $this->revokeAccessToken($request->getAttribute('oauth_access_token_id'));
    }

    /**
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
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new AccessTokenEntity($userIdentifier, $scopes);
        $token->setClient($clientEntity);
        return $token;
    }
}