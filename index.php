<?php

declare(strict_types=1);

use App\CommissionTask\Application;
use App\CommissionTask\Factory\Operation\OperationFactoryInterface;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Processor\ProcessorInterface;
use App\CommissionTask\Reader\Input\InputReaderInterface;
use App\CommissionTask\Util\OutputUtil;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $container = new Container();
    $container->init();
    /** @var OperationFactoryInterface $operationFactory */
    $operationFactory = $container->get('app.factory.operation');
    /** @var InputReaderInterface $inputReader */
    $inputReader = $container->get('app.reader.input.file');
    /** @var ProcessorInterface $operationProcessor */
    $operationProcessor = $container->get('app.processor.operation');
    $app = new Application($operationFactory, $inputReader, $operationProcessor);
    $app->run($argv);
} catch (Exception $e) {
    OutputUtil::writeLn('Error occurred');
    OutputUtil::writeLn($e->getMessage());
}
