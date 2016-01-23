<?php

$url    = 'http://temp.smolensk.ws/'; // путь к сраничке с температурой
$start  = '<font size=5 color=red>'; // начальная позиция для парсера
$finish = 'C</font>'; // конечная позиция парсера

function parser($url, $start, $finish)
{
    $w         = '"weather":';
    $content   = file_get_contents($url); // получаем содержимое странички
    $position1 = strpos($content, $start); // получаем позицию начала строки
    $position2 = strpos($content, $finish); // получаем позицию конца строки
    $content   = substr($content, $position1, $position2 - $position1); // убираем лишнее
    $content   = strip_tags($content, '<p><a>'); // чистим тэги, если имеются
    
    echo ($content); //выводим содержимое
    return $content; // возвращаем значение
}

parser($url, $start, $finish); // запуск парсера

?>
