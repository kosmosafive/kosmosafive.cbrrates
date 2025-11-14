<?php

declare(strict_types=1);

namespace Kosmosafive\CBRRates\Collection;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Type;
use InvalidArgumentException;
use Kosmosafive\Bitrix\DS\Collection;
use Kosmosafive\CBRRates\Entity\Currency;
use Kosmosafive\CBRRates\Exception\Exception;
use Kosmosafive\CBRRates\Http\ResponseInterface;
use Kosmosafive\CBRRates\ReturnPrototype\ReturnPrototypeInterface;
use Kosmosafive\CBRRates\ValueObject\Rate;

/**
 * @template-extends Collection<Rate>
 */
final class RateCollection extends Collection implements ReturnPrototypeInterface
{
    /**
     * @param Rate $value
     *
     * @return RateCollection
     */
    public function add(mixed $value): RateCollection
    {
        if (!$value instanceof Rate) {
            throw new InvalidArgumentException("This collection only accepts instances of " . Rate::class);
        }

        return $this;
    }

    /**
     * @throws ArgumentException
     * @throws ObjectException
     * @throws Exception
     */
    public static function createFromResponse(ResponseInterface $response): RateCollection
    {
        $content = $response->getContent();

        $collection = new RateCollection();

        if (isset($content['ValCurs']['#']) && is_string($content['ValCurs']['#'])) {
            throw new Exception($content['ValCurs']['#']);
        }

        if (!(
            isset($content['ValCurs']['#']['Valute'])
            && is_array($content['ValCurs']['#']['Valute'])
        )) {
            return $collection;
        }

        $request = $response->getRequest();
        $queryParameters = $request->getQueryParameters();
        $parameters = $queryParameters->export();

        $date = (isset($parameters['date_req']))
            ? new Type\Date($parameters['date_req'], 'd/m/Y')
            : new Type\Date();

        foreach ($content['ValCurs']['#']['Valute'] as $rate) {
            $collection->add(
                new Rate(
                    $date,
                    new Currency(
                        $rate['@']['ID'],
                        $rate['#']['CharCode'][0]['#'],
                        $rate['#']['NumCode'][0]['#'],
                        $rate['#']['Name'][0]['#']
                    ),
                    (float) str_replace(',', '.', $rate['#']['Value'][0]['#']),
                    (int) $rate['#']['Nominal'][0]['#']
                )
            );
        }

        return $collection;
    }
}
