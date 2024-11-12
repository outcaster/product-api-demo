<?php declare(strict_types=1);

namespace App\Tests\Presentation\Validation;

use App\Application\Query\ProductQuery;
use App\Infrastructure\Exception\ValidationException;
use App\Presentation\Validation\QueryValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueryValidatorTest extends TestCase
{
    private ValidatorInterface $validator;
    private QueryValidator $queryValidator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->queryValidator = new QueryValidator($this->validator);
    }

    public function testValidateProductQuerySuccess(): void
    {
        $productQuery = new ProductQuery();
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($productQuery)
            ->willReturn(new ConstraintViolationList());

        $this->queryValidator->validateProductQuery($productQuery);

        $this->addToAssertionCount(1); // Ensure the method completes without exceptions
    }

    public function testValidateProductQueryWithErrors(): void
    {
        $productQuery = new ProductQuery();
        $violation = new ConstraintViolation(
            'Invalid value',
            null,
            [],
            $productQuery,
            'category',
            'invalid-category'
        );
        $violations = new ConstraintViolationList([$violation]);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($productQuery)
            ->willReturn($violations);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->queryValidator->validateProductQuery($productQuery);
    }
}
