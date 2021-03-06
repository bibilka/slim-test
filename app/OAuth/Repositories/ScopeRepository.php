<?php

namespace App\OAuth\Repositories;

use App\OAuth\Entities\ScopeEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * Класс-репозиторий для работы с OAuth сущностью Scope.
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    const BASIC_SCOPE = 'basic';

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
        $scopes = [
            self::BASIC_SCOPE => [
                'description' => 'Basic details about you',
            ]
        ];

        if (\array_key_exists($scopeIdentifier, $scopes) === false) {
            return;
        }

        $scope = new ScopeEntity();
        $scope->setIdentifier($scopeIdentifier);

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        // ...
        return $scopes;
    }
}