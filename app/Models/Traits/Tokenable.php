<?php

namespace App\Models\Traits;

/**
 * Трейт, который указывает на то, что модель описывает сущность какого-либо токена.
 */
trait Tokenable
{
    /**
     * Является ли текущий токен аннулированным.
     * @return bool
     */
    public function isRevoked()
    {
        return $this->revoked == true;
    }

    /**
     * Аннулировать текущий токен.
     * @return bool
     */
    public function revoke()
    {
        $this->revoked = true;
        $this->save();
    }
}