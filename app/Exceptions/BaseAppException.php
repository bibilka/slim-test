<?php

namespace App\Exceptions;

use Slim\Exception\HttpException;

/**
 * Базовое исключение об ошибке, которое может возникнуть в приложении.
 */
class BaseAppException extends HttpException
{

}