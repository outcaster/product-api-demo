<?php declare(strict_types=1);

namespace App\Domain\Interfaces;

interface MoneyInterface
{
    public function instantiate(int $amount, string $currency): void;
    public function getAmount(): string;
    public function getCurrency(): string;
    public function multiply(float|int|string $multiplier): MoneyInterface;
    public function subtract(MoneyInterface $money): MoneyInterface;
}
