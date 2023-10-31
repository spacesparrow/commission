<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Charger\Withdraw;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Operation\Operation;

final class PrivateClientWithdrawFeeChargerDataProvider
{
    public static function dataProviderForChargeWhenLessThanFreeCountPerWeekTesting(): array
    {
        $firstOperation = self::getOperation('5');
        $secondOperation = self::getOperation('15');
        $thirdOperation = self::getOperation('0.5');
        $fourthOperation = self::getOperation('0');

        return [
            [$firstOperation, '0.50'],
            [$secondOperation, '1.50'],
            [$thirdOperation, '0.05'],
            [$fourthOperation, '0.00'],
        ];
    }

    public static function dataProviderForChargeWhenLessThanFreeAmountPerWeekTesting(): array
    {
        $firstOperation = self::getOperation('5');
        $secondOperation = self::getOperation('15');
        $thirdOperation = self::getOperation('0.5');
        $fourthOperation = self::getOperation('0');

        return [
            [$firstOperation, '0.00'],
            [$secondOperation, '0.00'],
            [$thirdOperation, '0.00'],
            [$fourthOperation, '0.00'],
        ];
    }

    public static function dataProviderForChargeWhenWholeOverFreeAmountPerWeekTesting(): array
    {
        $firstOperation = self::getOperation('5');
        $secondOperation = self::getOperation('15');
        $thirdOperation = self::getOperation('0.5');
        $fourthOperation = self::getOperation('150.00');

        return [
            [$firstOperation, '0.50'],
            [$secondOperation, '1.50'],
            [$thirdOperation, '0.05'],
            [$fourthOperation, '15.00'],
        ];
    }

    public static function dataProviderForChargeWhenPartiallyOverFreeAmountPerWeekTesting(): array
    {
        $firstOperation = self::getOperation('5');
        $secondOperation = self::getOperation('15');
        $thirdOperation = self::getOperation('2.5');
        $fourthOperation = self::getOperation('150.00');

        return [
            [$firstOperation, '0.45'],
            [$secondOperation, '1.45'],
            [$thirdOperation, '0.20'],
            [$fourthOperation, '14.95'],
        ];
    }

    public static function dataProviderForSupportsTesting(): array
    {
        $firstOperation = self::getOperation('5', Operation::TYPE_DEPOSIT);
        $secondOperation = self::getOperation('15');
        $thirdOperation = self::getOperation('0.5');
        $fourthOperation = self::getOperation('0', Operation::TYPE_DEPOSIT);

        return [
            [$firstOperation, false],
            [$secondOperation, true],
            [$thirdOperation, true],
            [$fourthOperation, false],
        ];
    }

    public static function getClient(): Client
    {
        return new Client(1, Client::TYPE_PRIVATE);
    }

    public static function getOperation(string $amount, string $operationType = Operation::TYPE_WITHDRAW): Operation
    {
        return new Operation('EUR', new \DateTime(), $amount, $operationType, self::getClient());
    }
}
