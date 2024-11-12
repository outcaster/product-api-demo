<?php

namespace App\Presentation\Adapter;

use App\Application\Query\ProductQuery;
use App\Presentation\Controller\ProductController;
use App\Presentation\Validation\QueryValidator;
use Symfony\Component\HttpFoundation\Request;

class ProductQueryAdapter
{
    public function __construct(
        private QueryValidator $queryValidator
    ) {
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
            (string)ProductController::DEFAULT_PAGE
        );
        $productQuery->limit = (int)$request->query->get(
            'limit',
            (string)ProductController::DEFAULT_PAGE_SIZE
        );

        $this->queryValidator->validateProductQuery($productQuery);
        return $productQuery;
    }
}
