<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Entity;

final readonly class Currency
{
    public function __construct(
        protected string $id,
        protected string $code,
        protected string $numericCode,
        protected string $name
    ) {

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNumericCode(): string
    {
        return $this->numericCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
