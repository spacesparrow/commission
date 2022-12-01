<?php

declare(strict_types=1);

use App\CommissionTask\Application;
use App\CommissionTask\Kernel\Container;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $container = new Container();
    $container->init();
    $app = new Application($container);
    $app->run($argv);
} catch (Exception $e) {
    echo 'Error occurred'.PHP_EOL.$e->getMessage().PHP_EOL;
}
