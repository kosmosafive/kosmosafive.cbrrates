# Kosmosafive: CBR Rates

Получение котировок валют Центрального банка Российской Федерации.

## Установка

В composer.json (пример для директории local) проекта добавьте

```json
{
  "require": {
    "wikimedia/composer-merge-plugin": "dev-master"
  },
  "config": {
    "allow-plugins": {
      "wikimedia/composer-merge-plugin": true
    }
  },
  "extra": {
    "merge-plugin": {
      "require": [
        "../bitrix/composer-bx.json",
        "modules/*/composer.json"
      ],
      "recurse": true,
      "replace": true,
      "ignore-duplicates": false,
      "merge-dev": true,
      "merge-extra": false,
      "merge-extra-deep": false,
      "merge-scripts": false
    },
    "installer-paths": {
      "modules/{$name}/": [
        "type:bitrix-d7-module"
      ]
    }
  }
}
```

- установить модуль

## Конфигурация модуля

Конфигурацию рекомендуется указывать в файле /bitrix/.settings_extra.php.

* _adapter_ — настройки адаптера

```php
return [
    'kosmosafive.cbrrates' => [
        'value' => [
            'adapter' => [
                'bitrix' => [
                    'http_client_options' => [
                        'redirect' => true,
                    ],
                ],
            ],
        ]
    ]
];
```

## Использование

Отправлять запросы можно как напрямую (сервис Kosmosafive\CBRRates\CBRApiInterface::class),
так и с помощью сервиса-обертки (сервис Kosmosafive\CBRRates\Service\ApiServiceInterface::class).

### Использование напрямую

1. Получить клиент

```php
use Bitrix\Main\Loader;
use Bitrix\Main\DI\ServiceLocator;
use Kosmosafive\CBRRates\CBRApiInterface;

$api = ServiceLocator::getInstance()->get(CBRApiInterface::class);
$client = $api->getClient();
```

2. Выполнить запрос

```php
$rateCollection = (new Route\Scripts\XmlDaily($client))->get();
```

### Использование сервиса-обертки

Реализованы методы:
- получение котировок на заданный на день | getDailyRates(?Type\Date $date = null)
- справочник по кодам валют | getCurrencies()
- обновление котировок на заданный день | updateDailyRates(Type\Date $date)

1. Получить сервис

```php
use Bitrix\Main\Loader;
use Bitrix\Main\DI\ServiceLocator;
use Kosmosafive\CBRRates\Service\ApiServiceInterface;

$apiService = ServiceLocator::getInstance()->get(ApiServiceInterface::class);
```

2. Выполнить запрос

```php
$rateCollection = $apiService->getDailyRates();
```

### Клиент

Поддерживается множество клиентов.
Клиент идентифицируется по ключу.
По умолчанию создается клиент с ключом default.
Добавить собственный клиент можно программно.

```php
use Kosmosafive\CBRRates\Options;

$clientKey = 'custom';

$options = new Options();
$client = new Client($options);

$addClientResult = $apiService->addClient($clientKey, $client);

$client = $apiService->getClient($clientKey);
```

### События

#### onGetClient

Вызывается при вызове метода **$apiService->getClient**, когда клиент не был найден.
В параметрах передается *key* — идентификатор запрошенного клиента.
Обратно необходимо вернуть Клиент в параметре *client*.

### Маршруты

- *Scripts*
- - XmlDaily: get
- - XmlValFul: get

### Логирование

Реализована поддержка логгера,
реализующего интерфейс [PSR-3](https://www.php-fig.org/psr/psr-3/),
указанного в конфигурации системы
([Разработчик Bitrix Framework: Логгеры](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=15330)).

Расширенный логгер доступен при реализации интерфейса *Kosmosafive\CBRRates\Diag\Logger\HttpLoggerInterface*:

```php
namespace Kosmosafive\CBRRates\Diag\Logger;

use Kosmosafive\CBRRates\Http\RequestInterface;
use Kosmosafive\CBRRates\Http\ResponseInterface;

interface HttpLoggerInterface
{

    public function logRequest(string $level, RequestInterface $request): void;

    public function logResponse(string $level, ResponseInterface $response): void;
}
```

Модуль содержит расширенную файловую реализацию логгера — *Kosmosafive\CBRRates\Diag\Logger\FileHttpLogger*

Поиск логгера будет осуществлен по ключам *kosmosafive.cbrrates* и *default*.

### Исключения

- Kosmosafive\CBRRates\Exception\Exception
- Kosmosafive\CBRRates\Http\Exception\RequestException

### Интерфейс командной строки

#### Обновление котировок за указанный день \ период

```bash
kosmosafive.cbrrates:update-daily-rates <from> [<to>]
```

- _from_ — Дата \ Дата начала периода (YYYY-MM-DD)
- _to_ — (опционально) Дата конца периода (YYYY-MM-DD)
