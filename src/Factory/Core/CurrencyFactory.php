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

    public function createFromData(array $data): CurrencyInterface
    {
        $currency = $this->createNew();
        $currency->setCode($data['code']);
        $currency->setRate($data['rate']);
        $currency->setBase($data['base']);

        return $currency;
    }
}
