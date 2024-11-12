<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Query\ProductQuery;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;

class ProductListApplicationService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @return array<Product>
     */
    public function __invoke(ProductQuery $query): array
    {
        return $this->productRepository->findProducts(
            $query->category,
            $query->priceLessThan,
            $query->page,
            $query->limit
        );
    }
}
