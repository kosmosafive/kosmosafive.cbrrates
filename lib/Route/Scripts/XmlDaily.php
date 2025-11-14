<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Route\Scripts;

use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Collection\RateCollection;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Route\AbstractRoute;
use Kosmosafive\CBRRates\ApiRequest;

class XmlDaily extends AbstractRoute
{
    /**
     * @throws Exception
     */
    public function get(?Type\Date $date = null): RateCollection
    {
        $parameters = [];
        if ($date) {
            $parameters['date_req'] = $date->format('d/m/Y');
        }

        /**
         * @var RateCollection
         */
        return (new ApiRequest(
            $this->getClient(),
            '/scripts/XML_daily.asp',
            RateCollection::class,
            $parameters
        ))->execute();
    }
}
