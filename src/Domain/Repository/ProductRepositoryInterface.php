<?php

namespace App\Domain\Repository;

interface ProductRepositoryInterface
{
    public function findProducts(?string $category, ?int $priceLessThan): array;
}
