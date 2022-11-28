<?php

declare(strict_types=1);

namespace App\CommissionTask;

use App\CommissionTask\Factory\Operation\OperationFactoryInterface;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Kernel\ContainerAwareInterface;
use App\CommissionTask\Kernel\ContainerAwareTrait;
use App\CommissionTask\Processor\ProcessorInterface;

final class Application implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct()
    {
        $this->setContainer(new Container());
        $this->getContainer()->init();
    }

    public function run(int $argc, array $argv): void
    {
        $this->getContainer()->get('app.reader.currency')->read();
        /** @var OperationFactoryInterface $operationFactory */
        $operationFactory = $this->getContainer()->get('app.factory.operation');
        $operationsData = $this->getContainer()->get('app.reader.input.file')->read($argv[1]);
        /** @var ProcessorInterface $operationProcessor */
        $operationProcessor = $this->getContainer()->get('app.processor.operation');

        foreach ($operationsData as $operationsDatum) {
            $operation = $operationFactory->createFromCsvRow($operationsDatum);
            $operationProcessor->process($operation);
        }
    }
}
