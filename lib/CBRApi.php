<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Error;
use Kosmosafive\Bitrix\Localization\Loc;
use Bitrix\Main\Result;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;
use Kosmosafive\CBRRates\Http\Client;
use Psr\Log;

class CBRApi implements CBRApiInterface
{
    private array $clients = [];

    private const string MODULE_ID = 'kosmosafive.cbrrates';
    public const string EVENT_ON_GET_CLIENT = 'onGetClient';

    public function __construct(
        protected Log\LoggerInterface $logger
    ) {
    }

    private function createDefaultClient(): ?Client
    {
        $options = new Options((array)Configuration::getValue(static::MODULE_ID));

        return new Client(
            $options,
            $this->logger
        );
    }

    public function addClient(string $key, Client $client): Result
    {
        $result = new Result();

        if (array_key_exists($key, $this->clients)) {
            return $result->addError(
                new Error(Loc::getMessage('Kosmosafive_USA_ERROR_CLIENT_ALREADY_EXISTS', ['#KEY#' => $key]))
            );
        }

        $this->clients[$key] = $client;

        return $result;
    }

    public function hasClient(string $key): bool
    {
        return array_key_exists($key, $this->clients);
    }

    public function getClient(string $key = 'default'): ?Client
    {
        if (!array_key_exists($key, $this->clients)) {
            if ($key === 'default') {
                $client = $this->createDefaultClient();
            } else {
                $client = null;

                $event = new Event(
                    static::MODULE_ID,
                    static::EVENT_ON_GET_CLIENT,
                    [
                        'key' => $key,
                    ]
                );
                $event->send();

                foreach ($event->getResults() as $eventResult) {
                    if ($eventResult->getType() === EventResult::ERROR) {
                        continue;
                    }

                    $eventParameters = $eventResult->getParameters();

                    $client = $eventParameters['client'];

                    break;
                }
            }

            $this->clients[$key] = $client;
        }

        return $this->clients[$key];
    }
}
