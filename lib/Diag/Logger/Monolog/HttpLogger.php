<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Diag\Logger\Monolog;

use Kosmosafive\CBRRates\Diag\Logger\HttpLoggerInterface;
use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;
use Monolog\Logger;

class HttpLogger extends Logger implements HttpLoggerInterface
{
    public function logRequest(string $level, RequestInterface $request): void
    {
        $message = 'Request';
        $context = [
            'path' => $request->getPath(),
            'method' => $request->getMethod(),
            'body' => $request->getBodyParameters()->getArrayCopy(),
            'query' => $request->getQueryParameters()->getArrayCopy(),
            'headers' => $request->getHeaders()->getArrayCopy(),
            'uri' => $request->getUri()->getUri(),
        ];

        $this->log($level, $message, $context);
    }

    public function logResponse(string $level, ResponseInterface $response): void
    {
        $message = 'Response';
        $context = [
            'body' => $response->getBody(),
            'content' => $response->getContent(),
            'headers' => $response->getHeaders()->getArrayCopy(),
            'statusCode' => $response->getStatusCode(),
        ];

        $this->log($level, $message, $context);
    }
}
