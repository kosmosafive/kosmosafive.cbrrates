<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\ReturnPrototype;

use Kosmosafive\CBRRates\Http\ResponseInterface;

interface ReturnPrototypeInterface
{
    public static function createFromResponse(ResponseInterface $response): ReturnPrototypeInterface;
}
