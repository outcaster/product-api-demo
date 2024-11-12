<?php declare(strict_types = 1);

namespace App\Tests\Presentation\Adapter;

    use App\Application\Query\ProductQuery;
    use App\Presentation\Adapter\ProductQueryAdapter;
    use App\Presentation\Validation\QueryValidator;
    use App\Infrastructure\Exception\ValidationException;
    use PHPUnit\Framework\TestCase;
    use Symfony\Component\HttpFoundation\Request;

class ProductQueryAdapterTest extends TestCase
{
    private QueryValidator $queryValidator;
    private ProductQueryAdapter $productQueryAdapter;

    protected function setUp(): void
    {
        $this->queryValidator = $this->createMock(QueryValidator::class);
        $this->productQueryAdapter = new ProductQueryAdapter($this->queryValidator);
    }

    /**
     * @dataProvider productQueryProvider
     */
    public function testGetProductQuery(array $queryParams, ProductQuery $expectedProductQuery): void
    {
        $request = new Request($queryParams);

        $this->queryValidator
            ->expects($this->once())
            ->method('validateProductQuery')
            ->with($this->isInstanceOf(ProductQuery::class));

        $productQuery = $this->productQueryAdapter->getProductQuery($request);

        $this->assertEquals($expectedProductQuery, $productQuery);
    }

    public function productQueryProvider(): array
    {
        $defaultProductQuery = new ProductQuery();
        $defaultProductQuery->page = 1;
        $defaultProductQuery->limit = 5;

        $categoryAndPriceProductQuery = new ProductQuery();
        $categoryAndPriceProductQuery->category = 'electronics';
        $categoryAndPriceProductQuery->priceLessThan = 500;
        $categoryAndPriceProductQuery->page = 2;
        $categoryAndPriceProductQuery->limit = 10;

        return [
            'default values' => [
                'queryParams' => [],
                'expectedProductQuery' => $defaultProductQuery,
            ],
            'with category and price' => [
                'queryParams' => [
                    'category' => 'electronics',
                    'priceLessThan' => '500',
                    'page' => '2',
                    'limit' => '10'
                ],
                'expectedProductQuery' => $categoryAndPriceProductQuery,
            ],
        ];
    }

    public function testGetProductQueryValidationErrors(): void
    {
        $queryParams = [
            'category' => 'invalid-category',
            'priceLessThan' => 'invalid-price',
            'page' => 'invalid-page',
            'limit' => 'invalid-limit'
        ];
        $request = new Request($queryParams);

        $this->queryValidator
            ->expects($this->once())
            ->method('validateProductQuery')
            ->will($this->throwException(
                new ValidationException(['error' => 'Invalid query parameters'])
            ));

        $this->expectException(ValidationException::class);

        $this->productQueryAdapter->getProductQuery($request);
    }
}
