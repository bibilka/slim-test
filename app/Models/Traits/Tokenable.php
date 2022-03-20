<?php

namespace App\Models\Traits;

trait Tokenable
{
    /**
     * Является ли текущий токен аннулированным.
     * @return bool
     */
    public function isRevoked()
    {
        return $this->revoke == true;
    }

    /**
     * Аннулировать текущий токен.
     * @return bool
     */
    public function revoke()
    {
        $this->revoke = true;
        $this->save();
    }
}