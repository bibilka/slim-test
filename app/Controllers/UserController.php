<?php

namespace App\Controllers;

use App\Controllers\BaseController as Controller;
use App\Models\AccessToken;
use App\Models\RefreshToken;

final class UserController extends Controller
{
    public function clearAllTokens($request, $response, array $args)
    {
        AccessToken::whereUserId($args['id'])->update(['revoke' => true]);
        return $this->jsonResponse($response, true, 'Токены пользователя ID='.$args['id'].' успешно аннулированы');
    }
}
