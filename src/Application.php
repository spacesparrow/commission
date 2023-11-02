<?php

declare(strict_types=1);

namespace App\CommissionTask;

use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Output\OutputInterface;
use App\CommissionTask\Processor\ProcessorInterface;
use App\CommissionTask\Reader\Input\InputReaderInterface;

final readonly class Application
{
    public function __construct(
        private OperationFactory $operationFactory,
        private InputReaderInterface $inputReader,
        private ProcessorInterface $operationProcessor,
        private OutputInterface $output
    ) {
    }

    /**
     * @throws \Exception
     */
    public function run(array $argv): void
    {
        $operationsData = $this->inputReader->read($argv[1]);

        foreach ($operationsData as $operationsDatum) {
            $operation = $this->operationFactory->createFromCsvRow($operationsDatum);
            $fee = $this->operationProcessor->process($operation);
            if ($fee !== '') {
                $this->output->writeLn($fee);
            }
        }
    }
}
