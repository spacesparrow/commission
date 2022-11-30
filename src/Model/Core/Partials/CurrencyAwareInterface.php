<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core\Partials;

use App\CommissionTask\Model\Core\CurrencyInterface;

interface CurrencyAwareInterface
{
    public function getCurrency(): CurrencyInterface;

    public function setCurrency(CurrencyInterface $currency): void;
}
