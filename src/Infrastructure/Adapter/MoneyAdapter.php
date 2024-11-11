<?php declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Interfaces\MoneyInterface;
use Money\Money;
use Money\Currency;

class MoneyAdapter implements MoneyInterface {
    private Money $money;

    public function __construct(
        ?int $amount = null,
        ?string $currency = null,
    ) {
        if ($amount !== null && $currency !== null) {
            $this->instantiate($amount, $currency);
        }
    }

    public function instantiate(int $amount, string $currency): void
    {
        $this->money = new Money($amount, new Currency($currency));
    }

    public function getAmount(): string {
        return $this->money->getAmount();
    }

    public function getCurrency(): string {
        return $this->money->getCurrency()->getCode();
    }

    public function multiply(float|int|string $multiplier): MoneyInterface {
        $newMoney = $this->money->multiply($multiplier);
        return new self((int)$newMoney->getAmount(), $newMoney->getCurrency()->getCode());
    }

    public function subtract(MoneyInterface $money): MoneyInterface {
        $newMoney = $this->money->subtract(new Money($money->getAmount(), new Currency($money->getCurrency())));
        return new self((int)$newMoney->getAmount(), $newMoney->getCurrency()->getCode());
    }
}

