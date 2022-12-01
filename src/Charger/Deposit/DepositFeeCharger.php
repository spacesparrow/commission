<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Deposit;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Converter\CurrencyConverterInterface;
use App\CommissionTask\Kernel\ConfigAwareTrait;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Model\Operation\OperationTypeAwareInterface;
use App\CommissionTask\Util\MoneyUtil;
use App\CommissionTask\Util\OutputUtil;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;

class DepositFeeCharger implements FeeChargerInterface
{
    use ConfigAwareTrait;

    protected CurrencyConverterInterface $currencyConverter;

    public function __construct(ConfigInterface $config, CurrencyConverterInterface $currencyConverter)
    {
        $this->setConfig($config);
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function charge(OperationInterface $operation): void
    {
        $operatingAmount = MoneyUtil::createMoneyFromOperation($operation);
        $fee = $operatingAmount->multipliedBy(
            $this->config->getConfigParamByName('parameters.fee.deposit.percent'),
            RoundingMode::UP
        );

        OutputUtil::writeLn($fee->getAmount());
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationTypeAwareInterface::TYPE_DEPOSIT;
    }
}
