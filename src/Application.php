<?php

declare(strict_types=1);

namespace App\CommissionTask;

use App\CommissionTask\Factory\Operation\OperationFactoryInterface;
use App\CommissionTask\Kernel\ContainerInterface;
use App\CommissionTask\Processor\ProcessorInterface;

final class Application
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function run(array $argv): void
    {
        $this->container->get('app.reader.currency')->read();
        /** @var OperationFactoryInterface $operationFactory */
        $operationFactory = $this->container->get('app.factory.operation');
        $operationsData = $this->container->get('app.reader.input.file')->read($argv[1]);
        /** @var ProcessorInterface $operationProcessor */
        $operationProcessor = $this->container->get('app.processor.operation');

        foreach ($operationsData as $operationsDatum) {
            $operation = $operationFactory->createFromCsvRow($operationsDatum);
            $operationProcessor->process($operation);
        }
    }
}
