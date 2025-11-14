<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http\Adapter;

use Kosmosafive\CBRRates\Http\Client;
use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;

interface AdapterInterface
{

    public function __construct(Client $client);

    public function getClient(): Client;

    public function getOptions(): Options;

    public function sendRequest(RequestInterface $request): ResponseInterface;
}
