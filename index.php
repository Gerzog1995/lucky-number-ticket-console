<?php

require_once 'vendor/autoload.php';

use Core\LuckyNumber;

//Сделать проверку чисел ввдененных на диапазон и на количество символов

$modelLuckyNumber = new LuckyNumber();
$modelLuckyNumber->gettingLuckyNumber(['first' => $argv[1], 'end' => $argv[2]])


?>