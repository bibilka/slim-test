<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\BaseController as Controller;

final class AppController extends Controller
{
    public function showProfilePage($request, $response)
    {
        $data = [
            'user' => User::firstOrFail(),
            'routes' => $this->routes,
        ];

        return $this->view->render($response, 'profile.twig', $data);
    }

    public function showHomePage($request, $response)
    {
        return $this->view->render($response, 'home.twig', ['routes' => $this->routes]);
    }
}
