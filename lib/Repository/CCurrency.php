<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Repository;

class CCurrency extends \CCurrency
{
    public static function clearCache(string $currencyCode): void
    {
        if (isset(self::$currencyCache[$currencyCode])) {
            unset(self::$currencyCache[$currencyCode]);
        }
    }
}
