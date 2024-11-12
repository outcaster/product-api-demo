<?php declare(strict_types=1);

namespace App\Tests\Presentation\Listener;

use App\Infrastructure\Exception\ValidationException;
use App\Presentation\Listener\ValidationExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ValidationExceptionListenerTest extends TestCase
{
    private ValidationExceptionListener $listener;

    protected function setUp(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->listener = new ValidationExceptionListener('test', $logger);
    }

    public function testOnKernelExceptionWithValidationException(): void
    {
        $exception = new ValidationException(['field' => 'Invalid value']);
        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['errors' => ['field' => 'Invalid value']]),
            $response->getContent()
        );
    }

    public function testOnKernelExceptionWithGenericException(): void
    {
        $exception = new \Exception('Internal Server Error');
        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertNull($response);
    }

    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
    }
}
