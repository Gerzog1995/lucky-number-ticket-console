<?php

require_once 'vendor/autoload.php';

use Core\LuckyNumber;

$modelLuckyNumber = new LuckyNumber();
?>

<pre>
<?php print_r($modelLuckyNumber->gettingLuckyNumber($_GET)); ?>
</pre>
