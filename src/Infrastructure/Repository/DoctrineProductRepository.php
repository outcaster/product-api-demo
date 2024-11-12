<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @return Product[]
     */
    public function findProducts(?string $category, ?int $priceLessThan, ?int $page = 1, ?int $limit = 5): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select('p', 'COALESCE(MAX(d.percentage), 0) as discount_percentage')
            ->from(Product::class, 'p')
            ->leftJoin('App\Domain\Entity\Discount',
                'd',
                'WITH',
                'p.category = d.category OR p.sku = d.sku'
            )
            ->groupBy('p.sku');

        if ($category !== null) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        if ($priceLessThan !== null) {
            $qb->andWhere('p.price <= :priceLessThan')
                ->setParameter('priceLessThan', $priceLessThan);
        }

        $qb->orderBy('p.price', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

        $query = $qb->getQuery();
        $results = $query->getResult();

        $products = [];
        foreach ($results as $result) {
            $product = $result[0];
            $discountPercentage = $result['discount_percentage'];
            $product->setDiscountPercentage($discountPercentage);
            $products[] = $product;
        }

        return $products;
    }
}
