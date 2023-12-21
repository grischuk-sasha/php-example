<?php

use App\Service\Calculator;

require __DIR__ . '/vendor/autoload.php';

if (empty($argv[1])) {
    echo "File path not provided.";
    exit(1);
}

$calculator = new Calculator();

foreach ($calculator->calculateCommission($argv[1]) as $commission) {
    echo $commission . PHP_EOL;
}
