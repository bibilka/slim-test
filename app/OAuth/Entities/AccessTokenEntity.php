<?php

namespace App\OAuth\Entities;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessTokenEntity implements AccessTokenEntityInterface
{
    use AccessTokenTrait, TokenEntityTrait, EntityTrait;

    public function __construct(string $userIdentifier, array $scopes = [])
    {
        $this->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }
}