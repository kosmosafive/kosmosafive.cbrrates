<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

use Bitrix\Main\Event;
use Kosmosafive\Bitrix\Diag\Timer;
use Kosmosafive\Bitrix\Diag\TimerDTO;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Kosmosafive\CBRRates\Http\Adapter\AdapterInterface;
use Kosmosafive\CBRRates\Http\Exception\RequestException;
use Kosmosafive\CBRRates\Diag\Logger\HttpLoggerInterface;
use Kosmosafive\CBRRates\Options;
use Kosmosafive\CBRRates\Http\Adapter\BitrixAdapter;
use Throwable;

class Client
{
    private Options $options;
    private LoggerInterface $logger;
    private ?Headers $headers;
    private ?AdapterInterface $adapter;
    private ?RequestInterface $requestPrototype;
    private ?ResponseInterface $responsePrototype;

    protected const string MODULE_ID = 'kosmosafive.cbrrates';
    protected const string EVENT_ON_REQUEST_EXECUTED = 'onRequestExecuted';

    public function __construct(
        Options $options,
        LoggerInterface $logger = null
    ) {
        $this->options = $options;
        $this->logger = $logger ?: new NullLogger();
        $this->headers = null;
        $this->adapter = null;
        $this->requestPrototype = null;
        $this->responsePrototype = null;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): Client
    {
        $this->logger = $logger;
        return $this;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getHeaders(): Headers
    {
        if ($this->headers === null) {
            $this->headers = new Headers();
        }

        return $this->headers;
    }

    public function setHeaders(Headers $headers): Client
    {
        $this->headers = $headers;
        return $this;
    }

    public function getAdapter(): AdapterInterface
    {
        if ($this->adapter === null) {
            $this->adapter = new BitrixAdapter($this, new Adapter\Options((array)$this->options->getAdapter()['bitrix']));
        }

        return $this->adapter;
    }

    public function setAdapter(AdapterInterface $adapter): Client
    {
        $this->adapter = $adapter;
        return $this;
    }

    public function getRequestPrototype(): RequestInterface
    {
        if ($this->requestPrototype === null) {
            $this->requestPrototype = new Request($this);
        }

        return $this->requestPrototype;
    }

    public function setRequestPrototype(RequestInterface $prototype): Client
    {
        $this->requestPrototype = $prototype;
        return $this;
    }

    public function createRequest(): RequestInterface
    {
        return $this->getRequestPrototype()->createClone();
    }

    public function getResponsePrototype(): ResponseInterface
    {
        if ($this->responsePrototype === null) {
            $this->responsePrototype = new Response();
        }

        return $this->responsePrototype;
    }

    public function setResponsePrototype(ResponseInterface $prototype): Client
    {
        $this->responsePrototype = $prototype;
        return $this;
    }

    public function createResponse(): ResponseInterface
    {
        return clone $this->getResponsePrototype();
    }

    /**
     * @throws Throwable
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if ($this->logger instanceof HttpLoggerInterface) {
            $this->logger->logRequest('debug', $request);
        }

        $timer = new Timer();

        $exception = null;

        try {
            $response = $this->getAdapter()->sendRequest($request);
            $response->setRequest($request);

            $timer->end();

            if ($response->getStatusCode() >= 400) {
                $exception = RequestException::create($response);
            }
        } catch (Throwable $e) {
            $timer->end();

            $exception = $e;
            $response = $this->createResponse();
            $response->setRequest($request);
        }

        if ($this->logger instanceof HttpLoggerInterface) {
            $this->logger->logResponse('debug', $response);
        }

        (new Event(
            static::MODULE_ID,
            static::EVENT_ON_REQUEST_EXECUTED,
            [
                'response' => $response,
                'timer' => TimerDTO::createFromTimer($timer),
                'exception' => $exception,
            ]
        ))->send();

        if ($exception) {
            throw $exception;
        }

        return $response;
    }
}
