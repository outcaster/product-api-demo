<?php declare(strict_types=1);

namespace App\Tests\Presentation\Controller;

use App\Application\Query\ProductQuery;
use App\Application\Service\ProductListApplicationService;
use App\Presentation\Adapter\ProductQueryAdapter;
use App\Presentation\Controller\ProductController;
use App\Tests\Mother\ProductMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

class ProductControllerTest extends TestCase
{
    private ProductListApplicationService $productListApplicationService;
    private ProductQueryAdapter $productQueryAdapter;
    private ProductController $productController;
    private \Faker\Generator $faker;

    protected function setUp(): void
    {
        $this->productListApplicationService = $this->createMock(ProductListApplicationService::class);
        $this->productQueryAdapter = $this->createMock(ProductQueryAdapter::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->faker = Factory::create();

        $this->productController = new ProductController(
            $this->productListApplicationService,
            $this->productQueryAdapter
        );

        $this->productController->setContainer($this->container);
    }

    public function testInvoke(): void
    {
        $request = new Request();
        $productQuery = new ProductQuery();

        // Generate random products using ProductMother
        $products = ProductMother::createMultiple(5);

        $this->productQueryAdapter
            ->method('getProductQuery')
            ->willReturn($productQuery);

        $this->productListApplicationService
            ->method('__invoke')
            ->with($productQuery)
            ->willReturn($products);

        $response = $this->productController->__invoke($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertCount(5, $responseData);

        foreach ($products as $index => $product) {
            $this->assertEquals($product->getSku(), $responseData[$index]['sku']);
            $this->assertEquals($product->getName(), $responseData[$index]['name']);
            $this->assertEquals($product->getCategory(), $responseData[$index]['category']);
            $this->assertEquals($product->getPrice(), $responseData[$index]['price']['original']);
        }
    }

    public function testInvokeValidationError(): void
    {
        // Simulate a request with invalid data
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/products', 'REQUEST_METHOD' => 'GET']);
        $request->query->set('invalid_param', 'invalid_value');

        $this->productQueryAdapter
            ->method('getProductQuery')
            ->willThrowException(new \InvalidArgumentException('Invalid query parameter'));

        $this->expectException(\InvalidArgumentException::class);
        $this->productController->__invoke($request);
    }
}
