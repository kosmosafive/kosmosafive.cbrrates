<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Diag\Logger;

use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;

interface HttpLoggerInterface
{

    public function logRequest(string $level, RequestInterface $request): void;

    public function logResponse(string $level, ResponseInterface $response): void;
}
