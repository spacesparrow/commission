<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Deposit;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
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
    public function charge(OperationInterface $operation): \Stringable|string
    {
        return Money::of(
            $operation->getAmount(),
            $operation->getCurrency(),
            null,
            RoundingMode::UP
        )->multipliedBy($this->feePercent, RoundingMode::UP)->getAmount();
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationInterface::TYPE_DEPOSIT;
    }
}
