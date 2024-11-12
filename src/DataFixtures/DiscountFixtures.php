<?php

namespace App\DataFixtures;

use App\Domain\Entity\Discount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiscountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $discounts = [
            [
                'id' => 1,
                'sku' => null,
                'category' => 'boots',
                'percentage' => 30,
            ],
            [
                'id' => 2,
                'sku' => "000003",
                'category' => null,
                'percentage' => 30,
            ],
        ];

        foreach ($discounts as $discount) {
            $discount = new Discount(
                $discount['id'],
                $discount['percentage'],
                $discount['sku'],
                $discount['category'],
            );

            $manager->persist($discount);
        }

        $manager->flush();
    }
}
