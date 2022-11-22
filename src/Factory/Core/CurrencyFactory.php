<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Core;

use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Core\CurrencyInterface;

class CurrencyFactory implements CurrencyFactoryInterface
{
    public function createNew(): CurrencyInterface
    {
        return new Currency();
    }

    public function createFromCodeAndRate(string $code, string $rate, bool $base): CurrencyInterface
    {
        $currency = $this->createNew();
        $currency->setCode($code);
        $currency->setRate($rate);
        $currency->setBase($base);

        return $currency;
    }
}
