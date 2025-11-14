<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Service;

use Bitrix\Main\Result;
use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Collection\CurrencyCollection;
use Kosmosafive\CBRRates\Collection\RateCollection;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\Client;
use Kosmosafive\CBRRates\Repository;
use Kosmosafive\CBRRates\Route;
use Psr\Log;

readonly class ApiService implements ApiServiceInterface
{
    public function __construct(
        protected Client $client,
        protected Repository\RateRepositoryInterface $rateRepository,
        protected Log\LoggerInterface $logger
    ) {
    }

    /**
     * @throws Exception
     */
    public function getDailyRates(?Type\Date $date = null): RateCollection
    {
        $this->logger->debug('getDailyRates', ['date' => $date]);

        $rateCollection = (new Route\Scripts\XmlDaily($this->client))->get($date);

        $this->logger->debug('getDailyRates', ['rateCollection' => $rateCollection]);

        return $rateCollection;
    }

    /**
     * @throws Exception
     */
    public function getCurrencies(): CurrencyCollection
    {
        $this->logger->debug('getCurrencies');

        $currencyCollection = (new Route\Scripts\XmlValFull($this->client))->get();

        $this->logger->debug('getCurrencies', ['currencyCollection' => $currencyCollection]);

        return $currencyCollection;
    }

    /**
     * @throws Exception
     */
    public function updateDailyRates(Type\Date $date): Result
    {
        return (new UseCase\UpdateDailyRates(
            $this->client,
            $this->rateRepository,
            $this->logger
        ))->execute($date);
    }
}
