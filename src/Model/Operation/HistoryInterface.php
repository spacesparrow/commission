<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

interface HistoryInterface
{
    /** @return array|OperationInterface[] */
    public function getOperations(): iterable;

    /** @param array|OperationInterface[] $operations */
    public function setOperations(iterable $operations): void;
}
