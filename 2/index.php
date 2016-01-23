<?php

if (!ini_get('display_errors')) {
    ini_set('display_errors', '0'); // для дебага выбрать 1, потом выставить 0
}

$url    = 'http://temp.smolensk.ws/'; // путь к сраничке с температурой
$start  = '<font size=5 color=red>'; // начальная позиция для парсера
$finish = ' C</font>'; // конечная позиция парсера
$result = 0;

function parser($url, $start, $finish)
{
    $content   = file_get_contents($url); // получаем содержимое странички
    $position1 = strpos($content, $start); // получаем позицию начала строки
    $position2 = strpos($content, $finish); // получаем позицию конца строки
    $content   = substr($content, $position1, $position2 - $position1); // убираем лишнее
    $content   = strip_tags($content, '<p><a>'); // чистим тэги, если имеются
    
    //echo  ($content); // выводим содержимое
    return $content; // возвращаем значение
}

$xmlurl = 'http://informer.gismeteo.ru/xml/26781_1.xml'; // воруем данные у информера Гисметео
$xml    = simplexml_load_file(rawurlencode($xmlurl)); // загружаем в переменную

/* Выбираем нужные данные */

$cloudiness    = $xml->REPORT->TOWN->FORECAST[0]->PHENOMENA->attributes()->cloudiness;
$precipitation = $xml->REPORT->TOWN->FORECAST[0]->PHENOMENA->attributes()->precipitation;
$rpower        = $xml->REPORT->TOWN->FORECAST[0]->PHENOMENA->attributes()->rpower;
$pressure      = $xml->REPORT->TOWN->FORECAST[0]->PRESSURE->attributes()->max;
$relwet        = $xml->REPORT->TOWN->FORECAST[0]->RELWET->attributes()->max;
$heat          = $xml->REPORT->TOWN->FORECAST[0]->HEAT->attributes()->max;
$wind          = $xml->REPORT->TOWN->FORECAST[0]->WIND->attributes()->max;

$tempws   = parser($url, $start, $finish); // запуск парсера
$tempws_s = parser($url, '<font size=4 color=#888888>', ' C</font>/'); // запуск парсера для юга
$tempws_n = parser($url, '<font size=4 color=#BBBBBB>', ' C</font>)'); // запуск парсера для севера

/* расчитываем время восхода и заказа */

$lat = 54.78278; // latitude: 54.78278
$lng = 32.04528; // longitude: 32.04528
$gmt = 3; // offset: +3 GMT
$zen = ini_get("date.sunrise_zenith"); // zenith ~= 90, получаем из php

$sunrise = date_sunrise(time(), SUNFUNCS_RET_STRING, $lat, $lng, $zen, $gmt); // расчет времени восхода солнца
$sunset  = date_sunset(time(), SUNFUNCS_RET_STRING, $lat, $lng, $zen, $gmt); // расчет времени захода солнца

/* выводим данные в json */

$arr = array(
    'tempws' => $tempws, // температура
    'tempws_s' => $tempws_s, // температура на южной стороне
    'tempws_n' => $tempws_n, // температура на северной стороне
    'cloudiness' => (string) $cloudiness, // облачность
    'precipitation' => (string) $precipitation, // тип осадков: 4 - дождь, 5 - ливень, 6,7 – снег, 8 - гроза, 9 - нет данных, 10 - без осадков
    'rpower' => (string) $rpower, // нтенсивность осадков, если они есть. 0 - возможен дождь/снег, 1 - дождь/снег
    'pressure' => (string) $pressure, // давление
    'relwet' => (string) $relwet, // относительная влажность воздуха, в %
    'heat' => (string) $heat, // комфорт - температура воздуха по ощущению одетого по сезону человека, выходящего на улицу
    'wind' => (string) $wind, // ветер
    'sunrise' => (string) $sunrise, // рассвет
    'sunset' => (string) $sunset // закат
);

echo json_encode($arr, JSON_FORCE_OBJECT); // Преобразуем в json и выводим на экран

?>
