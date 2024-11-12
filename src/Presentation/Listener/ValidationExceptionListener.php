<?php

declare(strict_types=1);

namespace App\Presentation\Listener;

use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse(
                ['errors' => $exception->getErrors()],
                $exception->getStatusCode()
            );

            $event->setResponse($response);
        }
    }
}
