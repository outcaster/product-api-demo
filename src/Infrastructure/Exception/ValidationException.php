<?php declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    /**
     * @var array<mixed>
     */
    private array $errors;

    /**
     * @param array<mixed> $errors
     */
    public function __construct(array $errors, int $statusCode = 400)
    {
        $this->errors = $errors;
        parent::__construct($statusCode, 'Validation failed');
    }

    /**
     * @return array<mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
