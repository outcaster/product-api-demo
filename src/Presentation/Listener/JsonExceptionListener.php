<?php

declare(strict_types=1);

namespace App\Presentation\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonExceptionListener
{
    private string $environment;
    private LoggerInterface $logger;

    public function __construct(string $environment, LoggerInterface $logger)
    {
        $this->environment = $environment;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        // In production, avoid exposing the actual exception message
        $message = $this->environment === 'prod' ? 'An error occurred' : $exception->getMessage();

        // Log the exception details
        $this->logger->error('Exception occurred', [
            'exception' => $exception,
            'status_code' => $statusCode,
        ]);

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
