<?php declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Interfaces\MoneyInterface;

class Product
{
    public const CURRENCY = 'EUR';

    public function __construct(
        private string                  $sku,
        private string                  $name,
        private string                  $category,
        private int                     $price,
        private int                     $discountPercentage = 0,
    ){}

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getDiscountPercentage(): int
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(int $discountPercentage): void
    {
        $this->discountPercentage = $discountPercentage;
    }

    public function toArray(MoneyInterface $moneyAdapter): array
    {
        return [
            'sku' => $this->getSku(),
            'name' => $this->getName(),
            'category' => $this->getCategory(),
            'price' => [
                'original' => $this->getPrice(),
                'final' => $this->getFinalPrice($moneyAdapter),
                'discount_percentage' => $this->discountPercentage > 0 ? $this->discountPercentage . '%' : null,
                'currency' => self::CURRENCY
            ]
        ];
    }

    public function getFinalPrice(MoneyInterface $moneyAdapter): int
    {
        $moneyAdapter->instantiate($this->price, 'EUR');
        $discountAmount = $moneyAdapter->multiply($this->discountPercentage / 100);
        return (int)$moneyAdapter->subtract($discountAmount)->getAmount();
    }
}
