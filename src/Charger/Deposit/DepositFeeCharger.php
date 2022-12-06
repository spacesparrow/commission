<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Deposit;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\Operation;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class DepositFeeCharger implements FeeChargerInterface
{
    public function __construct(private float $feePercent)
    {
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function charge(Operation $operation): \Stringable|string
    {
        return Money::of(
            $operation->getAmount(),
            $operation->getCurrency(),
            null,
            RoundingMode::UP
        )->multipliedBy($this->feePercent, RoundingMode::UP)->getAmount();
    }

    public function supports(Operation $operation): bool
    {
        return $operation->getType() === Operation::TYPE_DEPOSIT;
    }
}
