<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Core;

use App\CommissionTask\Factory\FactoryInterface;
use App\CommissionTask\Model\Core\CurrencyInterface;

interface CurrencyFactoryInterface extends FactoryInterface
{
    public function createNew(): CurrencyInterface;

    public function createFromData(array $data): CurrencyInterface;
}
