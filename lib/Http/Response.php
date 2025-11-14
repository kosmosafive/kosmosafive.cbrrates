<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

use Bitrix\Main\Text;
use CDataXML;
use RuntimeException;

class Response implements ResponseInterface
{
    private RequestInterface $request;
    private string $protocolVersion = '';
    private int $statusCode = 0;
    private string $reasonPhrase = '';
    private ?Headers $headers = null;
    private string $body = '';
    private mixed $content = null;

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function setRequest(RequestInterface $request): Response
    {
        $this->request = $request;
        return $this;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(string $protocolVersion): Response
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): Response
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase(string $reasonPhrase): Response
    {
        $this->reasonPhrase = $reasonPhrase;
        return $this;
    }

    public function getHeaders(): Headers
    {
        if ($this->headers === null) {
            $this->headers = new Headers();
        }

        return $this->headers;
    }

    public function setHeaders(Headers $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): Response
    {
        $this->body = $body;
        $this->content = null;
        return $this;
    }

    public function getContent(): array
    {
        if ($this->content === null) {
            $data = $this->getBody();

            $charset = 'windows-1251';
            $matches = [];
            if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $data, $matches)) {
                $charset = trim($matches[1]);
            }
            $data = preg_replace(
                [
                    "#<!DOCTYPE[^>]+?>#i",
                    "#<"."\\?XML[^>]+?\\?".">#i",
                ],
                '',
                $data
            );
            $data = Text\Encoding::convertEncoding($data, $charset, SITE_CHARSET);

            $cDataXML = new CDataXML();
            if ($cDataXML->LoadString($data) === false) {
                throw new RuntimeException('XML error');
            }

            $this->content = $cDataXML->GetArray();
        }

        return $this->content;
    }
}
