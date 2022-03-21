<?php

namespace App\Handlers;

use App\Exceptions\AuthErrorException;
use App\Exceptions\BaseAppException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\ValidationException;
use Slim\App;

/**
 * Класс-обработчик ошибок в приложении.
 */
final class ErrorHandler
{
    /**
     * @var App
     */
    protected App $app;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->app = $container->get(App::class);
        $this->validator = $container->get('validator');
    }

    /**
     * Точка входа для обработки ошибок.
     * @param Request $request
     * @param \Throwable $exception
     * @return Response
     */
    public function __invoke(Request $request, \Throwable $exception): Response 
    {
        // либо обрабатываем API исключение, либо обычное
        if ($this->wantsJson($request)) {
            return $this->handleApiError($exception);
        }

        return $this->handleDefaultError($exception);
    }

    /**
     * Обработчик ошибок API. Когда запрос хочет ответ в виде JSON.
     * @param \Throwable $exception
     * @return Response
     */
    private function handleApiError(\Throwable $exception) : Response
    {
        $statusCode = $this->getStatusCode($exception);

        if ($exception instanceof BaseAppException) {
            $message = $exception->getMessage();
        } else {
            $message = isDebugMode() ? $exception->getMessage() : $this->getMessage($statusCode);
        }

        $data = [
            'message' => $message,
            'status' => false,
            'code' => $statusCode,
        ];
        
        if ($exception instanceof ValidationException) {
            $data['errors'] = $this->validator->getErrors();
        }

        if (isDebugMode()) {
            $data['trace'] = $exception->getTrace();
        }

        $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write((string) $body);

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-type', 'application/problem+json');
    }

    /**
     * Обработчик ошибок стандартного сценария. Когда запрос не требует ответа в виде JSON.
     * @param \Throwable
     * @return Response
     */
    private function handleDefaultError(\Throwable $exception) : Response
    {
        $response = $this->app->getResponseFactory()->createResponse();
        if ($exception instanceof AuthErrorException) {
            return $response->withAddedHeader('Location', '/login');
        }
        $statusCode = $this->getStatusCode($exception);
        
        $message = isDebugMode() ? $statusCode . ' ' . $exception->getMessage() : $this->getMessage($statusCode);

        if (isDebugMode()) {
            dd([
                'exception' => $message,
                'trace' => $exception->getTraceAsString()
            ]);
        }

        $response->getBody()->write("<h3>$message</h3>");
        return $response;
    }

    /**
     * @param int $code
     * @return string Сообщение об ошибке
     */
    private function getMessage(int $code) : string
    {
        $defaultError = 'Произошла непридвиденная ошибка. Пожалуйста, попробуйте позже или обратитесь к администатору';

        $map = [
            401 => 'Отказано в доступе',
            404 => 'Не найдено',
            422 => 'Ошибка валидации',
        ];

        return $map[$code] ?? $defaultError;
    }

    /**
     * Определяет, требует ли запрос ответа в формате JSON.
     * @param Request $request
     * @return bool
     */
    private function wantsJson(Request $request) : bool
    {
        return str_contains($request->getHeaderLine('Accept'), 'json');
    }

    /**
     * Определяет статус-код ошибки.
     * @param \Throwable $exception
     * @return int
     */
    private function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof OAuthServerException) {
            return 401;
        }

        $statusCode = 500;
        if (is_int($exception->getCode()) &&
            $exception->getCode() >= 400 &&
            $exception->getCode() <= 500
        ) {
            $statusCode = $exception->getCode();
        }

        return $statusCode;
    }
}