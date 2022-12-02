<?php

declare(strict_types=1);

use App\CommissionTask\Application;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Util\OutputUtil;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $container = new Container();
    $container->init();
    $app = new Application($container);
    $app->run($argv);
} catch (Exception $e) {
    OutputUtil::writeLn('Error occurred');
    OutputUtil::writeLn($e->getMessage());
}
