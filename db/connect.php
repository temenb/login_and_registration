<?php

$link = mysqli_connect('localhost', 'root', '123123', 'site');

if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    exit;
}

return $link;