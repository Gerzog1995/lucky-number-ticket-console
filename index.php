<?php

require_once 'vendor/autoload.php';

use Core\LuckyNumber;

$modelLuckyNumber = new LuckyNumber();
$modelLuckyNumber->gettingLuckyNumber(['first' => $argv[1], 'end' => $argv[2]])