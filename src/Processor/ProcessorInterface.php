<?php

declare(strict_types=1);

namespace App\CommissionTask\Processor;

use App\CommissionTask\Model\Operation\OperationInterface;

interface ProcessorInterface
{
    public function process(OperationInterface $operation): void;
}
