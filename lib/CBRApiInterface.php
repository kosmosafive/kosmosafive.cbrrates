<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates;

use Bitrix\Main\Result;
use Kosmosafive\CBRRates\Http\Client;

interface CBRApiInterface
{
    public function addClient(string $key, Client $client): Result;

    public function hasClient(string $key): bool;

    public function getClient(string $key = 'default'): ?Client;
}
