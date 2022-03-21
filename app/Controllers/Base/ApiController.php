<?php

namespace App\Controllers\Base;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Psr7\Response;

abstract class ApiController
{
    protected App $app;

    public function __construct(ContainerInterface $container){
        $this->app = $container->get(App::class);
    }

    protected function jsonResponse(
        Response $response,
        bool $status,
        string $message = "",
        array $data = [],
        int $code = 200
    ): Response {
        $result = [
            'code' => $code,
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
        $payload = json_encode($result);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}