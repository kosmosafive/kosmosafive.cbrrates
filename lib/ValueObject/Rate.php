<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\ValueObject;

use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Entity\Currency;

final readonly class Rate
{
    public function __construct(
        protected Type\Date $date,
        protected Currency $currency,
        protected float $value,
        protected int $nominal
    ) {
    }

    public function getDate(): Type\Date
    {
        return $this->date;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getNominal(): int
    {
        return $this->nominal;
    }
}
