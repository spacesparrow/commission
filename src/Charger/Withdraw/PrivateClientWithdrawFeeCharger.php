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
use App\CommissionTask\Repository\RepositoryInterface;
use App\CommissionTask\Util\DateTimeUtil;
use App\CommissionTask\Util\MoneyUtil;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class PrivateClientWithdrawFeeCharger implements FeeChargerInterface
{
    use ConfigAwareTrait;

    protected CurrencyConverterInterface $currencyConverter;

    protected RepositoryInterface $operationRepository;

    public function __construct(
        ConfigInterface $config,
        CurrencyConverterInterface $currencyConverter,
        RepositoryInterface $operationRepository
    ) {
        $this->setConfig($config);
        $this->currencyConverter = $currencyConverter;
        $this->operationRepository = $operationRepository;
    }

    /**
     * @throws UnknownCurrencyException
     * @throws MoneyMismatchException
     */
    public function charge(OperationInterface $operation): void
    {
        $feePercent = $this->getConfig()->getConfigParamByName('parameters.fee.withdraw.private.percent');
        $freeCountPerWeek = (int) $this->getConfig()->getConfigParamByName('parameters.fee.withdraw.private.free_count_per_week');
        /** @var array $clientOperationsInWeek */
        $clientOperationsInWeek = $this->operationRepository->findUsingClosure($this->getClosureForSearch($operation));

        if ($freeCountPerWeek < count($clientOperationsInWeek) + 1) {
            echo MoneyUtil::createMoneyFromOperation($operation)
                    ->multipliedBy($feePercent, RoundingMode::UP)->getAmount().PHP_EOL;

            return;
        }

        $baseCurrencyCode = $this->getConfig()->getConfigParamByName('parameters.currency.base_currency_code');
        $convertedAmount = $this->currencyConverter->convert(
            $operation->getCurrency()->getCode(),
            $baseCurrencyCode,
            $operation->getAmount()
        );
        $feeChargingAmount = $this->getFeeChargingAmount($convertedAmount, $clientOperationsInWeek, $baseCurrencyCode);

        if ($feeChargingAmount->isNegativeOrZero()) {
            echo Money::zero($operation->getCurrency()->getCode())->getAmount().PHP_EOL;

            return;
        }

        $originalCurrencyFee = $this->currencyConverter->convert(
            $baseCurrencyCode,
            $operation->getCurrency()->getCode(),
            $feeChargingAmount->multipliedBy($feePercent)->__toString()
        );
        $originalCurrencyFee = Money::of(
            $originalCurrencyFee,
            $operation->getCurrency()->getCode(),
            null,
            RoundingMode::UP
        );

        echo $originalCurrencyFee->getAmount().PHP_EOL;
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationTypeAwareInterface::TYPE_WITHDRAW
            && $operation->getClient()->getType() === ClientTypeAwareInterface::TYPE_PRIVATE;
    }

    private function getClosureForSearch(OperationInterface $operation): callable
    {
        $client = $operation->getClient();
        $processedDate = clone $operation->getProcessedAt();
        $week = DateTimeUtil::getWeekIdentifier($processedDate);

        return static function (OperationInterface $operation) use ($client, $week) {
            return $operation->getType() === OperationTypeAwareInterface::TYPE_WITHDRAW
                && $operation->getClient() === $client
                && DateTimeUtil::getWeekIdentifier($operation->getProcessedAt()) === $week;
        };
    }

    /**
     * @throws UnknownCurrencyException
     * @throws MoneyMismatchException
     */
    private function getFeeChargingAmount(
        BigDecimal $convertedAmount,
        array $clientOperationsInWeek,
        string $baseCurrencyCode
    ): BigDecimal {
        $freeAmountPerWeek = Money::of(
            $this->getConfig()->getConfigParamByName('parameters.fee.withdraw.private.free_amount_per_week'),
            $baseCurrencyCode
        );
        $alreadySpent = Money::zero($baseCurrencyCode);

        /** @var OperationInterface $operationInWeek */
        foreach ($clientOperationsInWeek as $operationInWeek) {
            $alreadySpent = $alreadySpent->plus(
                Money::of(
                    $this->currencyConverter->convert(
                        $operationInWeek->getCurrency()->getCode(),
                        $baseCurrencyCode,
                        $operationInWeek->getAmount()
                    ),
                    $baseCurrencyCode
                ),
                RoundingMode::UP
            );
        }

        if ($alreadySpent->isGreaterThanOrEqualTo($freeAmountPerWeek)) {
            return $convertedAmount;
        }

        $willBeSpent = $alreadySpent->plus($convertedAmount, RoundingMode::UP);

        if ($willBeSpent->isGreaterThan($freeAmountPerWeek)) {
            return $willBeSpent->minus($freeAmountPerWeek, RoundingMode::UP)->getAmount();
        }

        return BigDecimal::zero();
    }
}
