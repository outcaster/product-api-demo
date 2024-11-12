<?php declare(strict_types=1);

namespace App\Tests\Mother;

use App\Domain\Entity\Product;
use Faker\Factory;
use Faker\Generator;

class ProductMother
{
    private static Generator $faker;

    public static function create(): Product
    {
        if (!isset(self::$faker)) {
            self::$faker = Factory::create();
        }

        return new Product(
            self::$faker->unique()->ean13,
            self::$faker->word,
            self::$faker->word,
            self::$faker->numberBetween(1000, 10000),
            self::$faker->numberBetween(0, 50)
        );
    }

    public static function createMultiple(int $count): array
    {
        $products = [];
        for ($i = 0; $i < $count; $i++) {
            $products[] = self::create();
        }
        return $products;
    }
}
