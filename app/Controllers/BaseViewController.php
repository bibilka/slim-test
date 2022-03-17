<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

abstract class BaseViewController
{
    protected Twig $view;
    protected $db;

    protected $pdo;

    public function __construct(ContainerInterface $container){
        $this->view = $container->get('view');
        $this->db = $container->get('db');
        $this->pdo = $container->get('pdo');
    }
}