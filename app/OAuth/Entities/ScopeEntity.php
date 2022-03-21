<?php

namespace App\OAuth\Entities;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

/**
 * OAuth сущность - Scope.
 */
class ScopeEntity implements ScopeEntityInterface
{
    use EntityTrait, ScopeTrait;
}