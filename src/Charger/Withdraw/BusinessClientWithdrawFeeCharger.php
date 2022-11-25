<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Withdraw;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Converter\CurrencyConverterInterface;
use App\CommissionTask\Kernel\ConfigAwareTrait;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Model\Client\ClientTypeAwareInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Model\Operation\OperationTypeAwareInterface;
use Brick\Math\RoundingMode;

class BusinessClientWithdrawFeeCharger implements FeeChargerInterface
{
    use ConfigAwareTrait;

    protected CurrencyConverterInterface $currencyConverter;

    public function __construct(ConfigInterface $config, CurrencyConverterInterface $currencyConverter)
    {
        $this->setConfig($config);
        $this->currencyConverter = $currencyConverter;
    }

    public function charge(OperationInterface $operation): void
    {
        $convertedAmount = $this->currencyConverter->convert(
            $operation->getCurrency()->getCode(),
            $this->config->getConfigParamByName('parameters.currency.base_currency_code'),
            $operation->getAmount()
        );
        $fee = $convertedAmount->multipliedBy(
            $this->config->getConfigParamByName('parameters.fee.withdraw.business.percent')
        );
        echo $fee->toScale(2, RoundingMode::UP).PHP_EOL;
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationTypeAwareInterface::TYPE_WITHDRAW
            && $operation->getClient()->getType() === ClientTypeAwareInterface::TYPE_BUSINESS;
    }
}
