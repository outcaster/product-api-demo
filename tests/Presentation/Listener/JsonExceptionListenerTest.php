<?php declare(strict_types=1);

namespace App\Presentation\Listener;

use App\Infrastructure\Exception\ValidationException;
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
        $response = new JsonResponse();
        $statusCode = 500;
        $error = ['message' => 'Internal Server Error', 'code' => $statusCode];

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $error = ['message' => $exception->getMessage(), 'code' => $statusCode];
        } elseif ($exception instanceof ValidationException) {
            $statusCode = 400;
            $error = [
                'message' => 'Validation failed',
                'code' => $statusCode,
                'details' => $exception->getErrors()
            ];
        }

        $response->setData(['error' => $error]);
        $response->setStatusCode($statusCode);

        $event->setResponse($response);
    }
}
