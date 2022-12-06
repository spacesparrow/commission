<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core;

interface CurrencyInterface extends ModelInterface
{
    public function getCode(): string;

    public function getRate(): string;

    public function isBase(): bool;
}
