<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates;

use ArrayObject;
use Kosmosafive\Bitrix\DS\ArrayObjectGetter;

/**
 * @method null|array getAdapter()
 */
class Options extends ArrayObject
{
    use ArrayObjectGetter;

    public function getApiUrl(): string
    {
        return 'https://www.cbr.ru';
    }
}
