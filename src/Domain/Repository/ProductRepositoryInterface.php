<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return array<Product>
     */
    public function findProducts(?string $category, ?int $priceLessThan, ?int $page = 1, ?int $limit = 5): array;
}
