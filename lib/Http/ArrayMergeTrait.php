<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

trait ArrayMergeTrait
{
    public function merge(array $data): void
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }
}
