<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Repository;

use Bitrix\Main\Result;
use Kosmosafive\CBRRates\Collection\RateCollection;

interface RateRepositoryInterface
{
    public function saveCollection(RateCollection $rateCollection): Result;
}
