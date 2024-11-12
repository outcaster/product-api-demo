<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Infrastructure\Repository\DoctrineProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;

class DoctrineProductRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private DoctrineProductRepository $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = new DoctrineProductRepository($this->entityManager);
    }

    public function testFindProducts(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $this
            ->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('leftJoin')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('groupBy')->willReturnSelf();
        $queryBuilder
            ->expects($this->exactly(2))
            ->method('andWhere')->willReturnSelf();
        $queryBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('orderBy')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setMaxResults')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setFirstResult')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('getQuery')->willReturn($query);
        $query->expects($this->once())->method('getResult')->willReturn([
            [
                new Product(
                    'SKU123',
                    'Test Product',
                    'Category',
                    100
                ), 'discount_percentage' => 20
            ]
        ]);

        $products = $this->repository->findProducts('Category', 150, 1, 5);

        $this->assertCount(1, $products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals('SKU123', $products[0]->getSku());
        $this->assertEquals(20, $products[0]->getDiscountPercentage());
    }

    public function testFindProductsNoCategory(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $this
            ->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('leftJoin')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('groupBy')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('andWhere')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('orderBy')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setMaxResults')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setFirstResult')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('getQuery')->willReturn($query);
        $query->expects($this->once())->method('getResult')->willReturn([
            [
                new Product(
                    'SKU123',
                    'Test Product',
                    'Category',
                    100
                ), 'discount_percentage' => 20
            ]
        ]);

        $products = $this
            ->repository
            ->findProducts(null, 150, 1, 5);

        $this->assertCount(1, $products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals('SKU123', $products[0]->getSku());
        $this->assertEquals(20, $products[0]->getDiscountPercentage());
    }

    public function testFindProductsNoPriceLessThan(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('select')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('from')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('leftJoin')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('groupBy')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('andWhere')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('orderBy')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setMaxResults')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setFirstResult')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('getQuery')->willReturn($query);
        $query->expects($this->once())->method('getResult')->willReturn([
            [
                new Product(
                'SKU123',
                'Test Product',
                'Category',
                100
                ), 'discount_percentage' => 20
            ]
        ]);

        $products = $this->repository->findProducts(
            'Category',
            null,
            1,
            5
        );

        $this->assertCount(1, $products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals('SKU123', $products[0]->getSku());
        $this->assertEquals(20, $products[0]->getDiscountPercentage());
    }
}
