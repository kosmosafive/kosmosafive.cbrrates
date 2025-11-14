<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Service;

use Bitrix\Main\Result;
use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Collection\CurrencyCollection;
use Kosmosafive\CBRRates\Collection\RateCollection;

interface ApiServiceInterface
{
    public function getDailyRates(?Type\Date $date = null): RateCollection;

    public function getCurrencies(): CurrencyCollection;

    public function updateDailyRates(Type\Date $date): Result;
}
