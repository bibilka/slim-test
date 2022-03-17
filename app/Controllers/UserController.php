<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\BaseController as Controller;

final class UserController extends Controller
{
    public function clearAllTokens($request, $response, array $args)
    {
        $user = User::whereId($args['id'])->firstOrFail();
        // ... clear $user tokens
    }
}
