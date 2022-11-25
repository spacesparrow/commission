<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Currency;

interface CurrencyReaderInterface
{
    public function read(): void;
}
