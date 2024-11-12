<?php declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\ProductListApplicationService;
use App\Infrastructure\Adapter\MoneyAdapter;
use App\Presentation\Adapter\ProductQueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    public const int DEFAULT_PAGE_SIZE = 5;
    public const int DEFAULT_PAGE = 1;

    public function __construct(
        private readonly ProductListApplicationService $productListApplicationService,
        private readonly ProductQueryAdapter $productQueryAdapter,
    ) {}

    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $productQuery = $this->productQueryAdapter->getProductQuery($request);

        $products = ($this->productListApplicationService)($productQuery);

        return $this->json(
            array_map(fn($product) => $product->toArray(new MoneyAdapter()), $products)
        );
    }
}
