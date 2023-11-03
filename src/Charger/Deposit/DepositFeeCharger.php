<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Deposit;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationType;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

readonly class DepositFeeCharger implements FeeChargerInterface
{
    public function __construct(private float $feePercent)
    {
    }

    /**
     * @throws UnknownCurrencyException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
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
        return $operation->getType() === OperationType::DEPOSIT;
    }
}
