<?php

declare(strict_types=1);

namespace App\CommissionTask\Converter;

use App\CommissionTask\Model\Core\CurrencyInterface;
use Brick\Math\BigDecimal;

interface CurrencyConverterInterface
{
    public function convert(string $from, string $to, string $amount): BigDecimal;

    public function getBaseCurrency(): CurrencyInterface;
}
