<?php

namespace App\Controllers\Base;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Psr7\Response;

/**
 * Абстрактный класс, предоставляющий базовые возможности для API контроллеров приложения.
 */
abstract class ApiController
{
    /**
     * @var App $app Объект приложения
     */
    protected App $app;

    /**
     * Инициализация.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->app = $container->get(App::class);
    }

    /**
     * Возвращает ответ в формате JSON.
     * 
     * @param Response $response
     * @param bool $status Успешный ответ или нет
     * @param string $message Сообщение к ответу (если требуется)
     * @param array $data Массив данных (если требуется)
     * @param int $code HTTP-СтатусКод ответа (по умолчанию 200)
     * 
     * @return Response
     */
    protected function jsonResponse(Response $response, bool $status, string $message = "", array $data = [], int $code = 200): Response 
    {
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