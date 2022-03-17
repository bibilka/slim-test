<?php

namespace App\Controllers;

use App\Controllers\BaseController as Controller;

class AuthController extends Controller
{
    public function showLoginPage($request, $response)
    {
        return $this->view->render($response, 'login.twig', ['routes' => $this->routes]);
    }

    public function doAuth()
    {
        // ...
    }

    public function showRegisterPage($request, $response)
    {
        return $this->view->render($response, 'register.twig', ['routes' => $this->routes]);
    }

    public function doSignUp()
    {
        // ...
    }

    public function logout($request, $response)
    {
        // ... do logout
        return $response->withHeader('Location', $this->routes['loginPage'])->withStatus(302);
    }

    public function refreshToken($request, $response)
    {
        // ...
    }
}
    