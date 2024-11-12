<?php

namespace App\Presentation\Validation;

use App\Application\Query\ProductQuery;
use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueryValidator
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function validateProductQuery(ProductQuery $productQuery): void
    {
        $errors = $this->validator->validate($productQuery);
        if (count($errors) > 0) {
            $errorDetails = [];
            foreach ($errors as $error) {
                $errorDetails[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            throw new ValidationException($errorDetails);
        }
    }
}
