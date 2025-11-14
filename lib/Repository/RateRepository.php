<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Repository;

use Bitrix\Currency;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type;
use Kosmosafive\CBRRates\Collection\RateCollection;
use Kosmosafive\CBRRates\ValueObject\Rate;

class RateRepository implements RateRepositoryInterface
{
    protected const string CURRENCY_CODE_RUB = 'RUB';

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function saveCollection(RateCollection $rateCollection): Result
    {
        $collection = Currency\CurrencyRateTable::createCollection();

        $currencyCodeList = Currency\CurrencyManager::getCurrencyList();
        $currencyCodesForUpdate = [];

        foreach ($rateCollection as $rate) {
            $currencyCode = $rate->getCurrency()->getCode();

            if (!array_key_exists($currencyCode, $currencyCodeList)) {
                continue;
            }

            $currencyCodesForUpdate[$currencyCode] = null;
            $collection->add($this->getObjByRate($rate));
        }

        $result = $collection->save();

        if ($result->isSuccess()) {
            Currency\CurrencyTable::cleanCache();

            foreach (array_keys($currencyCodesForUpdate) as $currencyCode) {
                Currency\CurrencyManager::updateBaseRates($currencyCode);
                CCurrency::clearCache($currencyCode);
            }

            Currency\CurrencyManager::clearCurrencyCache();
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    protected function getObjByRate(Rate $rate): Currency\EO_CurrencyRate
    {
        $query = Currency\CurrencyRateTable::query()
            ->where('CURRENCY', $rate->getCurrency()->getCode())
            ->where('BASE_CURRENCY', static::CURRENCY_CODE_RUB)
            ->where('DATE_RATE', $rate->getDate())
            ->setLimit(1);

        $execQuery = $query->exec();

        $now = new Type\Datetime();

        if (!$obj = $execQuery->fetchObject()) {
            $obj = Currency\CurrencyRateTable::createObject();
            $obj->setCurrency($rate->getCurrency()->getCode());
            $obj->setBaseCurrency(static::CURRENCY_CODE_RUB);
            $obj->setDateRate($rate->getDate());
            $obj->setDateCreate($now);
        }

        $obj->setRate($rate->getValue());
        $obj->setRateCnt($rate->getNominal());
        $obj->setTimestampX($now);

        return $obj;
    }
}
