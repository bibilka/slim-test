<?php

namespace App\Exceptions;

/**
 * Исключение, выбрасываемое при ошибках связанных с авторизацией.
 */
class AuthErrorException extends BaseAppException
{
    /**
     * @var int
     */
    protected $code = 401;
}