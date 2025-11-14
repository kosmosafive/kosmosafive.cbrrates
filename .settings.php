<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Loader;
use Kosmosafive\Bitrix\Diag\Logger\LoggerFactory;
use Kosmosafive\CBRRates\CBRApi;
use Kosmosafive\CBRRates\CBRApiInterface;
use Kosmosafive\CBRRates\Cli\Command;
use Kosmosafive\CBRRates\Service;
use Kosmosafive\CBRRates\Repository;

$services = [
    CBRApiInterface::class => [
        'constructor' => static function () {
            return new CBRApi(
                LoggerFactory::create('kosmosafive.cbrrates'),
            );
        },
    ],
];

if (Loader::includeModule('currency')) {
    $services[Service\ApiServiceInterface::class] = [
        'constructor' => static function () {
            $client = ServiceLocator::getInstance()->get(CBRApiInterface::class)->getClient();

            return new Service\ApiService(
                $client,
                new Repository\RateRepository(),
                LoggerFactory::create('kosmosafive.cbrrates')
            );
        },
    ];
}

return [
    'services' => [
        'value' => $services,
    ],
    'console' => [
        'value' => [
            'commands' => [
                Command\UpdateDailyRates::class,
            ],
        ],
        'readonly' => true,
    ],
];
