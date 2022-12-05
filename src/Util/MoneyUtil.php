<?php

declare(strict_types=1);

namespace App\CommissionTask\Util;

use App\CommissionTask\Model\Operation\OperationInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class MoneyUtil
{
    /**
     * @throws UnknownCurrencyException
     */
    public static function createMoneyFromOperation(OperationInterface $operation): Money
    {
        return Money::of(
            $operation->getAmount(),
            $operation->getCurrency(),
            null,
            RoundingMode::UP
        );
    }
}
