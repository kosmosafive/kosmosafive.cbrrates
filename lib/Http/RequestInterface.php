<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

use Bitrix\Main\Web\Uri;

interface RequestInterface
{
    public const string METHOD_GET = 'GET';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_HEAD = 'HEAD';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_OPTIONS = 'OPTIONS';

    public function __construct(Client $client);

    public function getClient(): Client;

    public function getHeaders(): Headers;

    public function setHeaders(Headers $headers): RequestInterface;

    public function getMethod(): string;

    public function setMethod(string $method): RequestInterface;

    public function getPath(): string;

    public function setPath(string $path): RequestInterface;

    public function getQueryParameters(): Parameters;

    public function setQueryParameters(Parameters $queryParameters): RequestInterface;

    public function getBodyParameters(): Parameters;

    public function setBodyParameters(Parameters $bodyParameters): RequestInterface;

    public function getUri(): Uri;

    public function execute(): ResponseInterface;

    public function createClone(): RequestInterface;
}
