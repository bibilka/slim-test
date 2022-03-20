<?php

namespace App\Exceptions;

class AuthErrorException extends BaseAppException
{
    /**
     * @var int
     */
    protected $code = 401;
}