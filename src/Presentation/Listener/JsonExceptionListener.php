<?php declare(strict_types=1);

namespace App\Presentation\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonExceptionListener
{
    private string $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $message = $this->environment === 'prod' ? 'An error occurred' : $exception->getMessage();

        $response = new JsonResponse(
            [
                'error' => [
                    'message' => $message,
                    'code' => $statusCode,
                ]
            ],
            $statusCode
        );

        $event->setResponse($response);
    }
}
