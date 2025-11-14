<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Http;

use Bitrix\Main\ArgumentException;
use ArrayObject;
use Bitrix\Main\Web\Json;

class Parameters extends ArrayObject
{
    use ArrayMergeTrait;

    /**
     * @throws ArgumentException
     */
    public function export(): array
    {
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = (is_null($value) || is_scalar($value)) ? $value : $this->exportNonScalar($value);
        }

        return $data;
    }

    /**
     * @throws ArgumentException
     */
    protected function exportNonScalar($value): string
    {
        return Json::encode($value);
    }
}
