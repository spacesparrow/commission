<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Withdraw;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Operation\Operation;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class BusinessClientWithdrawFeeCharger implements FeeChargerInterface
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
        return $operation->getType() === Operation::TYPE_WITHDRAW
            && $operation->getClient()->getType() === Client::TYPE_BUSINESS;
    }
}
