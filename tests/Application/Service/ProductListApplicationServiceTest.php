<?php declare(strict_types=1);

namespace App\Tests\Application\Service;

use App\Application\Query\ProductQuery;
use App\Application\Service\ProductListApplicationService;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Tests\Mother\ProductMother;
use PHPUnit\Framework\TestCase;

class ProductListApplicationServiceTest extends TestCase
{
    private ProductListApplicationService $service;
    private ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->service = new ProductListApplicationService($this->productRepository);
    }

    public function testGetProductList(): void
    {
        $expectedProducts = ProductMother::createMultiple(5);
        $productQuery = new ProductQuery();

        $this->productRepository
            ->method('findProducts')
            ->willReturn($expectedProducts);

        $result = $this->service->__invoke($productQuery);

        $this->assertEquals($expectedProducts, $result);
    }
}
