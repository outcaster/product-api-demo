<?php

namespace App\DataFixtures;

use App\Domain\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'sku' => '000001',
                'name' => 'BV Lean leather ankle boots',
                'category' => 'boots',
                'price' => 89000,
            ],
            [
                'sku' => '000002',
                'name' => 'BV Lean leather ankle boots',
                'category' => 'boots',
                'price' => 99000,
            ],
            [
                'sku' => '000003',
                'name' => 'Ashlington leather ankle boots',
                'category' => 'boots',
                'price' => 71000,
            ],
            [
                'sku' => '000004',
                'name' => 'Naima embellished suede sandals',
                'category' => 'sandals',
                'price' => 79500,
            ],
            [
                'sku' => '000005',
                'name' => 'Nathane leather sneakers',
                'category' => 'sneakers',
                'price' => 59000,
            ],
            [
                'sku' => '000006',
                'name' => 'Nathane ankle sneakers',
                'category' => 'sneakers',
                'price' => 69000,
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product(
                $productData['sku'],
                $productData['name'],
                $productData['category'],
                $productData['price'],
            );

            $manager->persist($product);
        }

        $manager->flush();
    }
}
