
###подключение
```
composer require grkvnko/mango-connector
```

###использование

```php
use grkvnko\MangoConnector\MangoAPIConnector;

$mango = new MangoAPIConnector([
    'key' => ' -- key here -- ',
    'api_salt' => ' -- api_salt here -- ',
    'widget_api' => 'https://widgets-api.mango-office.ru/v1/calltracking/НОМЕР/',
    'widget_api_key' => ' -- widget_api_key here -- '
]);


$selectStartDay = \DateTime::createFromFormat('Y-m-d', "2020-01-23", new \DateTimeZone('UTC'));
$selectEndDay = $selectStartDay->modify('+1 day');

// получение списка звонков из виджета колтрекинга
$getCalls = $mango->getCalls([
    'dateStart' => $selectStartDay->format('Y-m-d\TH:i').'Z',
    'dateEnd' => $selectEndDay->format('Y-m-d\TH:i').'Z',
]);

$request_id = hash('md5', 'request'.rand(0,100));

// подготовка статистики
$stats_request = $mango->stats_request([
    "date_from" => $selectStartDay->getTimestamp(),
    "date_to" => $selectEndDay->getTimestamp(),
    "fields" => "records, start, finish, answer, from_extension, from_number, to_extension, to_number, disconnect_reason, line_number, location",
    "request_id"=> $request_id
]);
$statsResult = json_decode($stats_request, true);

// пауза 2 секунды для приготовления статистики
sleep(2);

// получение CSV приготовленной статистики
$res = $mango->stats_result([
    "key" => $statsResult['key'], 
    "request_id" => $request_id
]);

```