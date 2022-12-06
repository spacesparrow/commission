<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger;

use App\CommissionTask\Model\Operation\Operation;

interface FeeChargerInterface
{
    public function charge(Operation $operation): \Stringable|string;

    public function supports(Operation $operation): bool;
}
