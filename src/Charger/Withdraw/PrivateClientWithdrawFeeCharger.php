<?php

declare(strict_types=1);

namespace App\CommissionTask\Charger\Withdraw;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Converter\CurrencyConverterInterface;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Repository\RepositoryInterface;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;

class PrivateClientWithdrawFeeCharger implements FeeChargerInterface
{
    public function __construct(
        private CurrencyConverterInterface $currencyConverter,
        private RepositoryInterface $operationRepository,
        private float $feePercent,
        private float $freeCountPerWeek,
        private int $freeAmountPerWeek,
        private string $baseCurrencyCode
    ) {
    }

    /**
     * @throws UnknownCurrencyException
     * @throws MoneyMismatchException
     */
    public function charge(OperationInterface $operation): \Stringable|string
    {
        /** @var array $clientOperationsInWeek */
        $clientOperationsInWeek = $this->operationRepository->findUsingClosure($this->getClosureForSearch($operation));

        if ($this->freeCountPerWeek < count($clientOperationsInWeek) + 1) {
            return Money::of(
                $operation->getAmount(),
                $operation->getCurrency(),
                null,
                RoundingMode::UP
            )->multipliedBy($this->feePercent, RoundingMode::UP)->getAmount();
        }

        $operationAmount = Money::of(
            $operation->getAmount(),
            $operation->getCurrency(),
            null,
            RoundingMode::UP
        )->getAmount();
        $feeChargingAmount = $this->getFeeChargingAmount(
            $operationAmount,
            $clientOperationsInWeek,
            $this->baseCurrencyCode,
            $operation->getCurrency()
        );

        if ($feeChargingAmount->isNegativeOrZero()) {
            return Money::zero($operation->getCurrency())->getAmount();
        }

        return Money::of(
            $feeChargingAmount->multipliedBy($this->feePercent),
            $operation->getCurrency(),
            null,
            RoundingMode::UP
        )->getAmount();
    }

    public function supports(OperationInterface $operation): bool
    {
        return $operation->getType() === OperationInterface::TYPE_WITHDRAW
            && $operation->getClient()->getType() === ClientInterface::TYPE_PRIVATE;
    }

    private function getClosureForSearch(OperationInterface $operation): callable
    {
        $client = $operation->getClient();
        $processedDate = clone $operation->getProcessedAt();
        $week = $this->getWeekIdentifier($processedDate);

        return function (OperationInterface $operation) use ($client, $week) {
            return $operation->getType() === OperationInterface::TYPE_WITHDRAW
                && $operation->getClient() === $client
                && $this->getWeekIdentifier($operation->getProcessedAt()) === $week;
        };
    }

    private function getWeekIdentifier(\DateTimeInterface $date): string
    {
        $dayOfWeek = $date->format('w');
        $date->modify('- '.(($dayOfWeek - 1 + 7) % 7).'days');
        $sunday = clone $date;
        $sunday->modify('+ 6 days');

        return "{$date->format('Y-m-d')}-{$sunday->format('Y-m-d')}";
    }

    /**
     * @throws UnknownCurrencyException
     * @throws MoneyMismatchException
     */
    private function getFeeChargingAmount(
        BigDecimal $operationAmount,
        array $clientOperationsInWeek,
        string $baseCurrencyCode,
        string $originalCurrencyCode
    ): BigDecimal {
        $freeAmountPerWeek = Money::of(
            $this->freeAmountPerWeek,
            $baseCurrencyCode
        );
        $alreadySpent = Money::zero($baseCurrencyCode);

        /** @var OperationInterface $operationInWeek */
        foreach ($clientOperationsInWeek as $operationInWeek) {
            $alreadySpent = $alreadySpent->plus(
                Money::of(
                    $this->currencyConverter->convert(
                        $operationInWeek->getCurrency(),
                        $baseCurrencyCode,
                        $operationInWeek->getAmount()
                    ),
                    $baseCurrencyCode
                ),
                RoundingMode::UP
            );
        }

        if ($alreadySpent->isGreaterThanOrEqualTo($freeAmountPerWeek)) {
            return $operationAmount;
        }

        $convertedAmount = $this->currencyConverter->convert(
            $originalCurrencyCode,
            $baseCurrencyCode,
            (string) $operationAmount
        );

        $willBeSpent = $alreadySpent->plus($convertedAmount, RoundingMode::UP);

        if ($willBeSpent->isGreaterThan($freeAmountPerWeek)) {
            return $this->currencyConverter->convert(
                $baseCurrencyCode,
                $originalCurrencyCode,
                (string) $willBeSpent->minus($freeAmountPerWeek, RoundingMode::UP)->getAmount()
            );
        }

        return BigDecimal::zero();
    }
}
