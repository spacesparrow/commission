<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Withdraw;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Util\MoneyUtil;
use App\CommissionTask\Util\OutputUtil;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;

class BusinessClientWithdrawFeeCharger implements FeeChargerInterface
{
    public function __construct(private float $feePercent)
    {
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function charge(OperationInterface $operation): void
    {
        $operatingAmount = MoneyUtil::createMoneyFromOperation($operation);
        $fee = $operatingAmount->multipliedBy($this->feePercent, RoundingMode::UP);

        OutputUtil::writeLn($fee->getAmount());
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationInterface::TYPE_WITHDRAW
            && $operation->getClient()->getType() === ClientInterface::TYPE_BUSINESS;
    }
}
