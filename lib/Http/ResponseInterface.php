<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

interface ResponseInterface
{
    public function getRequest(): RequestInterface;

    public function setRequest(RequestInterface $request): ResponseInterface;

    public function getProtocolVersion(): string;

    public function setProtocolVersion(string $protocolVersion): ResponseInterface;

    public function getStatusCode(): int;

    public function setStatusCode(int $statusCode): ResponseInterface;

    public function getHeaders(): Headers;

    public function setHeaders(Headers $headers): ResponseInterface;

    public function getBody(): string;

    public function setBody(string $body): ResponseInterface;

    public function getContent();

    public function getReasonPhrase(): string;

    public function setReasonPhrase(string $reasonPhrase): ResponseInterface;
}
