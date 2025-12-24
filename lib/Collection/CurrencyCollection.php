<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Collection;

use InvalidArgumentException;
use Kosmosafive\Bitrix\DS\Collection;
use Kosmosafive\CBRRates\Http\ResponseInterface;
use Kosmosafive\CBRRates\ReturnPrototype\ReturnPrototypeInterface;
use Kosmosafive\CBRRates\Entity\Currency;

/**
 * @template-extends Collection<Currency>
 */
final class CurrencyCollection extends Collection implements ReturnPrototypeInterface
{
    /**
     * @param Currency $value
     *
     * @return CurrencyCollection
     */
    public function add(mixed $value): CurrencyCollection
    {
        if (!$value instanceof Currency) {
            throw new InvalidArgumentException("This collection only accepts instances of " . Currency::class);
        }

        return parent::add($value);
    }

    public static function createFromResponse(ResponseInterface $response): CurrencyCollection
    {
        $content = $response->getContent();

        $collection = new CurrencyCollection();

        if (!(
            isset($content['Valuta']['#']['Item'])
            && is_array($content['Valuta']['#']['Item'])
        )) {
            return $collection;
        }

        foreach ($content['Valuta']['#']['Item'] as $currency) {
            $collection->add(
                new Currency(
                    $currency['@']['ID'],
                    $currency['#']['ISO_Char_Code'][0]['#'],
                    $currency['#']['ISO_Num_Code'][0]['#'],
                    $currency['#']['Name'][0]['#']
                )
            );
        }

        return $collection;
    }
}
