<?php

namespace Kosmosafive\CBRRates;

use Kosmosafive\Bitrix\Localization\Loc;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\Client;
use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;
use Kosmosafive\CBRRates\ReturnPrototype\ReturnPrototypeInterface;

readonly class ApiRequest
{
    private string $returnPrototypeClass;

    /**
     * @throws Exception
     */
    public function __construct(
        private Client $client,
        private string $path,
        string $returnPrototypeClass,
        private array $parameters = [],
        private string $method = RequestInterface::METHOD_GET
    ) {
        if (
            $returnPrototypeClass
            && !array_key_exists(ReturnPrototypeInterface::class, class_implements($returnPrototypeClass))
        ) {
            throw new Exception(Loc::getMessage('Kosmosafive_USA_API_REQUEST_ERROR_RETURN_PROTOTYPE_CLASS'));
        }

        $this->returnPrototypeClass = $returnPrototypeClass;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function execute(): ReturnPrototypeInterface
    {
        $request = $this->prepareRequest();
        $response = $this->executeRequest($request);

        return $this->createObject($response);
    }

    protected function prepareRequest(): RequestInterface
    {
        $request = $this->getClient()->createRequest();

        $request->setPath($this->path);
        $request->setMethod($this->method);

        if (!empty($this->parameters)) {
            $requestParams = ($this->method === RequestInterface::METHOD_GET)
                ? $request->getQueryParameters() : $request->getBodyParameters();

            $requestParams->merge($this->parameters);
        }

        return $request;
    }

    protected function executeRequest(RequestInterface $request): ResponseInterface
    {
        return $request->execute();
    }

    protected function createObject(ResponseInterface $response): ReturnPrototypeInterface
    {
        /** @var class-string<ReturnPrototypeInterface> $returnPrototypeClass */
        $returnPrototypeClass = $this->returnPrototypeClass;

        return $returnPrototypeClass::createFromResponse($response);
    }
}
