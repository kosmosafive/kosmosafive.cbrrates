<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Route\Scripts;

use Kosmosafive\CBRRates\ApiRequest;
use Kosmosafive\CBRRates\Route\AbstractRoute;
use Kosmosafive\CBRRates\Collection\CurrencyCollection;
use Kosmosafive\CBRRates\Exception\Exception;

class XmlValFull extends AbstractRoute
{
    /**
     * @throws Exception
     */
    public function get(): CurrencyCollection
    {
        /**
         * @var CurrencyCollection
         */
        return (new ApiRequest(
            $this->getClient(),
            '/scripts/XML_valFull.asp',
            CurrencyCollection::class
        ))->execute();
    }
}

