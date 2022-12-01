<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Model\Operation\OperationInterface;

interface OperationFactoryInterface
{
    public function createNew(): OperationInterface;

    public function createFromCsvRow(array $csvRow): OperationInterface;
}
