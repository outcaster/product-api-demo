<?php declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Query\ProductQuery;
use App\Application\Service\ProductListApplicationService;
use App\Infrastructure\Adapter\MoneyAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public const int DEFAULT_PAGE_SIZE = 5;
    public const int DEFAULT_PAGE = 1;

    public function __construct(
        private readonly ProductListApplicationService $productListApplicationService,
    ) {}

    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $productQuery = $this->getProductQuery($request);
        $products = ($this->productListApplicationService)($productQuery);

        return $this->json(
            array_map(fn($product) => $product->toArray(new MoneyAdapter()), $products)
        );
    }

    public function getProductQuery(Request $request): ProductQuery
    {
        $productQuery = new ProductQuery();
        $productQuery->category = $request->query->get('category');
        $priceLessThan = $request->query->get('priceLessThan');
        if ($priceLessThan !== null) {
            $productQuery->priceLessThan = (int)$priceLessThan;
        }

        $productQuery->page = (int)$request->query->get(
            'page',
            self::DEFAULT_PAGE
        );
        $productQuery->limit = (int)$request->query->get(
            'limit',
            self::DEFAULT_PAGE_SIZE
        );

        return $productQuery;
    }
}
