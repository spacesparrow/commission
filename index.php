<?php

declare(strict_types=1);

use App\CommissionTask\Application;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = new Application();
    $app->run($argc, $argv);
} catch (Exception $e) {
    echo 'Error occurred'.PHP_EOL.$e->getMessage().PHP_EOL;
}
