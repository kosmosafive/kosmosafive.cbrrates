<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Web\Uri;
use Kosmosafive\CBRRates\Http\Exception\RequestException;
use Throwable;

class Request implements RequestInterface
{
    private ?Headers $headers = null;
    private string $method;
    private ?Parameters $queryParameters = null;
    private ?Parameters $bodyParameters = null;
    private string $path;

    public function __construct(private readonly Client $client)
    {
        $this->method = static::METHOD_GET;
    }


    public function getClient(): Client
    {
        return $this->client;
    }

    public function getHeaders(): Headers
    {
        if ($this->headers === null) {
            $this->headers = clone $this->getClient()->getHeaders();
        }

        return $this->headers;
    }


    public function setHeaders(Headers $headers): Request
    {
        $this->headers = $headers;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): Request
    {
        $this->method = $method;
        return $this;
    }

    public function getQueryParameters(): Parameters
    {
        if ($this->queryParameters === null) {
            $this->queryParameters = new Parameters();
        }

        return $this->queryParameters;
    }


    public function setQueryParameters(Parameters $queryParameters): Request
    {
        $this->queryParameters = $queryParameters;
        return $this;
    }


    public function getBodyParameters(): Parameters
    {
        if ($this->bodyParameters === null) {
            $this->bodyParameters = new Parameters();
        }

        return $this->bodyParameters;
    }

    public function setBodyParameters(Parameters $bodyParameters): Request
    {
        $this->bodyParameters = $bodyParameters;
        return $this;
    }

    /**
     * @throws ArgumentException
     */
    public function getUri(): Uri
    {
        $uri = new Uri($this->getClient()->getOptions()->getApiUrl() . $this->getPath());

        if ($this->getQueryParameters()->count()) {
            $uri->addParams($this->getQueryParameters()->export());
        }

        return $uri;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Request
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @throws RequestException
     * @throws Throwable
     */
    public function execute(): ResponseInterface
    {
        return $this->getClient()->sendRequest($this);
    }

    public function createClone(): Request
    {
        return clone $this;
    }
}
