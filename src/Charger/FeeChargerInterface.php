<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger;

use App\CommissionTask\Model\Operation\OperationInterface;

interface FeeChargerInterface
{
    public function charge(OperationInterface $operation): void;

    public function supports(OperationInterface $operation): bool;
}
