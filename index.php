<?php

require_once 'vendor/autoload.php';

use Core\LuckyNumber;

$modelLuckyNumber = new LuckyNumber();
$listLuckyNumbers = $modelLuckyNumber->gettingLuckyNumber(['first' => $argv[1], 'end' => $argv[2]]);
?>

<?php print_r($listLuckyNumbers); ?>
Количество счастливых билетов: <?= $modelLuckyNumber->getCountLuckyTicket($listLuckyNumbers) . PHP_EOL; ?>
