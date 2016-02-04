<?php

if (!ini_get('display_errors')) {
    ini_set('display_errors', '0'); // для дебага выбрать 1, потом выставить 0
}

$urlimg = 'http://mini.s-shot.ru/1024x1024/PNG/1024/Z100/RND273102086968719/?www.meteorad.ru%2Fscreenshots%2Fuvk.html%3Fcode%3DRUDL'; //скриншотим страничку осадков
$imgout = 'precipitation.jpg'; // задаем имя файла, в который сохранять картинку

if (file_exists($imgout)) { // если файл существует
    $modify_date  = date("Y-m-d H:i:s", filemtime($imgout)); // дата последнего изменения картинки
    $current_date = date("Y-m-d H:i:s", time()); // текущая дата
    $seconds      = strtotime($current_date) - strtotime($modify_date); // разница в секундах
    $minutes      = round($seconds / 60, 2); // переводим в минуты
    if ($minutes > 15) { // если прошло больше 15 минут
        copy($urlimg, $imgout); //  обновляем картинку 
    }
} else { // если файл не существует
    copy($urlimg, $imgout); // обновляем картинку
}


$timetoactual = 15 - $minutes; // вычисляем сколько времени осталось до обновления

$arr = array(
    'fileurl' => $imgout, // скриншот имя файла
    'teimetoactual' => $timetoactual // времени осталось до обновления
);

echo json_encode($arr, JSON_FORCE_OBJECT); // Преобразуем в json и выводим на экран	

?>