<?php

namespace App\Controllers;

use App\Models\User;

final class HomeController extends BaseViewController
{
    public function profile($request, $response)
    {
        dd(User::all()->first()->name);
        return $this->view->render($response, 'home.twig', ['test' => '234']);
    }
}
