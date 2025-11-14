<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Service\UseCase;

use Bitrix\Main\Result;
use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\Client;
use Kosmosafive\CBRRates\Route;
use Kosmosafive\CBRRates\Repository;
use Psr\Log;

readonly class UpdateDailyRates
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
    public function execute(Type\Date $date): Result
    {
        $result = new Result();

        $this->logger->debug('UpdateDailyRates', ['date' => $date]);

        $rateCollection = (new Route\Scripts\XmlDaily($this->client))->get($date);

        $this->logger->debug('UpdateDailyRates', ['rateCollection' => $rateCollection, 'date' => $date]);

        $saveResult = $this->rateRepository->saveCollection($rateCollection);
        if (!$saveResult->isSuccess()) {
            $this->logger->error('UpdateDailyRates', ['saveResult' => $saveResult, 'date' => $date]);
            return $result->addErrors($saveResult->getErrors());
        }

        $this->logger->debug('UpdateDailyRates', ['saveResult' => $saveResult, 'date' => $date]);

        return $result;
    }
}
