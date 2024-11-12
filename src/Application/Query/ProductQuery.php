<?php

declare(strict_types=1);

namespace App\Application\Query;

class ProductQuery
{
    public ?string $category = null;
    public ?int $priceLessThan = null;
    public ?int $page = 1;
    public ?int $limit = 5;
}
