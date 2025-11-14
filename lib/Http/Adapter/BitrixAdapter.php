<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http\Adapter;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\Headers;
use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;

class BitrixAdapter extends AbstractAdapter
{
    /**
     * @throws Exception
     * @throws ArgumentException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $httpClient = new HttpClient($this->options['http_client_options']);

        $httpClient->setHeaders((array) $request->getHeaders());

        $isSuccess = $httpClient->query(
            $request->getMethod(),
            $request->getUri()->getUri(),
            Json::encode((array) $request->getBodyParameters())
        );
        if (!$isSuccess) {
            throw new Exception($this->getFormattedHttpError($httpClient->getError()));
        }

        $response = $this->getClient()->createResponse();

        $response->setBody($httpClient->getResult());
        $response->setProtocolVersion((string) $httpClient->getResponse()?->getProtocolVersion());
        $response->setStatusCode($httpClient->getStatus());
        $response->setReasonPhrase($httpClient->getResponse()?->getReasonPhrase());

        $headers = new Headers();
        foreach ($httpClient->getHeaders() as $key => $header) {
            foreach ($header as $value) {
                $headers[$key] = $value;
            }
        }
        $response->setHeaders($headers);

        return $response;
    }

    protected function getFormattedHttpError(array $responseError): string
    {
        return implode(
            '; ',
            array_map(static fn ($key, $error) => $key . ': ' . $error, array_keys($responseError), $responseError)
        );
    }
}
