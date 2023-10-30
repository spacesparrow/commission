<?php

declare(strict_types=1);

use App\CommissionTask\Application;
use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Output\OutputInterface;
use App\CommissionTask\Processor\ProcessorInterface;
use App\CommissionTask\Reader\Input\InputReaderInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new Container();
$container->init();
/** @var OutputInterface $consoleOutput */
$consoleOutput = $container->get('app.output.console');

try {

    /** @var OperationFactory $operationFactory */
    $operationFactory = $container->get('app.factory.operation');
    /** @var InputReaderInterface $inputReader */
    $inputReader = $container->get('app.reader.input.file');
    /** @var ProcessorInterface $operationProcessor */
    $operationProcessor = $container->get('app.processor.operation');
    $app = new Application($operationFactory, $inputReader, $operationProcessor, $consoleOutput);
    $app->run($argv);
} catch (Exception $e) {
    $consoleOutput->writeLn('Error occurred');
    $consoleOutput->writeLn($e->getMessage());
}
