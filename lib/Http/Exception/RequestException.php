<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http\Exception;

use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;
use Throwable;

class RequestException extends Exception
{
    protected RequestInterface $request;
    protected string $title = '';
    protected string $description = '';

    public function __construct(
        RequestInterface $request,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    public static function create(ResponseInterface $response): RequestException
    {
        $exceptionClass = static::getExceptionClass($response);

        $message = $response->getReasonPhrase();

        return new $exceptionClass($response->getRequest(), $message);
    }

    protected static function getExceptionClass(ResponseInterface $response): string
    {
        return static::class;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getContext(): array
    {
        return [
            'uri' => $this->request->getUri()->getUri(),
            'method' => $this->request->getMethod(),
            'body' => $this->request->getBodyParameters()->getArrayCopy(),
            'headers' => $this->request->getHeaders()->getArrayCopy(),
        ];
    }
}
