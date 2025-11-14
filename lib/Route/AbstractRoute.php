<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Route;

use Kosmosafive\CBRRates\Http\Client;

abstract class AbstractRoute
{
    public function __construct(private readonly Client $client)
    {
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
