<?php declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Product;
use App\Infrastructure\Adapter\MoneyAdapter;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @dataProvider productDataProvider
     */
    public function testToArray(array $productData, array $expectedArray, int $finalPrice): void
    {
        $moneyAdapter = $this->createMock(MoneyAdapter::class);
        $moneyAdapter->method('instantiate')->willReturnSelf();
        $moneyAdapter->method('multiply')->willReturnSelf();
        $moneyAdapter->method('subtract')->willReturnSelf();
        $moneyAdapter->method('getAmount')->willReturn((string)$finalPrice);

        $product = new Product(
            $productData['sku'],
            $productData['name'],
            $productData['category'],
            $productData['price'],
            $productData['discountPercentage']
        );

        $this->assertEquals($expectedArray, $product->toArray($moneyAdapter));
    }

    /**
     * @dataProvider productDataProvider
     */
    public function testGetFinalPrice(array $productData, array $expectedArray, int $finalPrice): void
    {
        $moneyAdapter = $this->createMock(MoneyAdapter::class);
        $moneyAdapter->method('instantiate')->willReturnSelf();
        $moneyAdapter->method('multiply')->willReturnSelf();
        $moneyAdapter->method('subtract')->willReturnSelf();
        $moneyAdapter->method('getAmount')->willReturn((string)$finalPrice);

        $product = new Product(
            $productData['sku'],
            $productData['name'],
            $productData['category'],
            $productData['price'],
            $productData['discountPercentage']
        );

        $this->assertEquals($finalPrice, $product->getFinalPrice($moneyAdapter));
    }

    public function productDataProvider(): array
    {
        return [
            'with discount' => [
                'productData' => [
                    'sku' => 'SKU123',
                    'name' => 'Test Product',
                    'category' => 'Category',
                    'price' => 100,
                    'discountPercentage' => 20
                ],
                'expectedArray' => [
                    'sku' => 'SKU123',
                    'name' => 'Test Product',
                    'category' => 'Category',
                    'price' => [
                        'original' => 100,
                        'final' => 80,
                        'discount_percentage' => '20%',
                        'currency' => 'EUR'
                    ]
                ],
                'finalPrice' => 80
            ],
            'without discount' => [
                'productData' => [
                    'sku' => 'SKU123',
                    'name' => 'Test Product',
                    'category' => 'Category',
                    'price' => 100,
                    'discountPercentage' => 0
                ],
                'expectedArray' => [
                    'sku' => 'SKU123',
                    'name' => 'Test Product',
                    'category' => 'Category',
                    'price' => [
                        'original' => 100,
                        'final' => 100,
                        'discount_percentage' => null,
                        'currency' => 'EUR'
                    ]
                ],
                'finalPrice' => 100
            ]
        ];
    }
}
