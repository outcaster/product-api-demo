<?php declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Query\ProductQuery;
use App\Domain\Repository\ProductRepositoryInterface;

readonly class ProductListApplicationService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function __invoke(ProductQuery $query): array
    {
        return $this->productRepository->findProducts($query->category, $query->priceLessThan);
    }
}
