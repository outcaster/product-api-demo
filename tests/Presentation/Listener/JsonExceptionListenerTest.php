<?php declare(strict_types=1);

namespace App\Tests\Presentation\Listener;

use App\Presentation\Listener\JsonExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonExceptionListenerTest extends TestCase
{
    private JsonExceptionListener $listener;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testOnKernelExceptionInProdEnvironment(): void
    {
        $this->listener = new JsonExceptionListener('prod', $this->logger);
        $exception = new \Exception('Test exception');
        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => ['message' => 'An error occurred', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR]]),
            $response->getContent()
        );
    }

    public function testOnKernelExceptionInNonProdEnvironment(): void
    {
        $this->listener = new JsonExceptionListener('dev', $this->logger);
        $exception = new \Exception('Test exception');
        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => ['message' => 'Test exception', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR]]),
            $response->getContent()
        );
    }

    public function testOnKernelExceptionWithHttpException(): void
    {
        $this->listener = new JsonExceptionListener('dev', $this->logger);
        $exception = new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Bad request');
        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => ['message' => 'Bad request', 'code' => JsonResponse::HTTP_BAD_REQUEST]]),
            $response->getContent()
        );
    }

    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
    }
}
