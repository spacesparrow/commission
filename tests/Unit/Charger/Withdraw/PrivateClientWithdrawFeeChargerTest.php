<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Charger\Withdraw;

use App\CommissionTask\Charger\Withdraw\PrivateClientWithdrawFeeCharger;
use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Storage\ArrayStorage;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PrivateClientWithdrawFeeChargerTest extends TestCase
{
    /**
     * @throws Exception
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws CurrencyConversionException
     * @throws MoneyMismatchException
     * @throws UnknownCurrencyException
     */
    #[DataProviderExternal(
        PrivateClientWithdrawFeeChargerDataProvider::class,
        'dataProviderForChargeWhenLessThanFreeCountPerWeekTesting'
    )]
    public function testChargeWhenLessThanFreeCountPerWeek(Operation $operation, string $expectedCharge): void
    {
        $currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $currencyConverterMock->expects($this->never())->method('convert');
        $charger = $this->getCharger($currencyConverterMock, [], 0, 5);
        $this->assertSame($expectedCharge, (string)$charger->charge($operation));
    }

    /**
     * @throws CurrencyConversionException
     * @throws Exception
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    #[DataProviderExternal(
        PrivateClientWithdrawFeeChargerDataProvider::class,
        'dataProviderForChargeWhenLessThanFreeAmountPerWeekTesting'
    )]
    public function testChargeWhenLessThanFreeAmountPerWeek(Operation $operation, string $expectedCharge): void
    {
        $currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $currencyConverterMock->expects($this->once())
            ->method('convert')
            ->willReturn(BigDecimal::of($operation->getAmount()));
        $charger = $this->getCharger($currencyConverterMock, [], 500);
        $this->assertSame($expectedCharge, (string)$charger->charge($operation));
    }

    /**
     * @throws CurrencyConversionException
     * @throws Exception
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    #[DataProviderExternal(
        PrivateClientWithdrawFeeChargerDataProvider::class,
        'dataProviderForChargeWhenWholeOverFreeAmountPerWeekTesting'
    )]
    public function testChargeWhenWholeOverFreeAmountPerWeekTesting(Operation $operation, string $expectedCharge): void
    {
        $currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $currencyConverterMock->expects($this->never())->method('convert');
        $charger = $this->getCharger($currencyConverterMock, [$operation], 0);
        $this->assertSame($expectedCharge, (string)$charger->charge($operation));
    }

    /**
     * @throws CurrencyConversionException
     * @throws Exception
     * @throws MathException
     * @throws MoneyMismatchException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    #[DataProviderExternal(
        PrivateClientWithdrawFeeChargerDataProvider::class,
        'dataProviderForChargeWhenPartiallyOverFreeAmountPerWeekTesting'
    )]
    public function testChargeWhenPartiallyOverFreeAmountPerWeekTesting(Operation $operation, string $expectedCharge): void
    {
        $currencyConverterMock = $this->createMock(CurrencyConverter::class);
        $currencyConverterMock->expects($this->exactly(2))
            ->method('convert')
            ->willReturnOnConsecutiveCalls(
                BigDecimal::of($operation->getAmount()),
                BigDecimal::of($operation->getAmount())->plus(BigDecimal::of(0.5)->minus(BigDecimal::of(1)))
            );
        $charger = $this->getCharger(
            $currencyConverterMock,
            [PrivateClientWithdrawFeeChargerDataProvider::getOperation('0.5')],
            1
        );
        $this->assertSame($expectedCharge, (string)$charger->charge($operation));
    }

    /**
     * @throws Exception
     */
    #[DataProviderExternal(PrivateClientWithdrawFeeChargerDataProvider::class, 'dataProviderForSupportsTesting')]
    public function testSupports(Operation $operation, bool $expectedResult): void
    {
        $charger = $this->getMockBuilder(PrivateClientWithdrawFeeCharger::class)
            ->onlyMethods(['charge'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertSame($expectedResult, $charger->supports($operation));
    }

    /**
     * @throws Exception
     */
    private function getStorageMock(array $expectedReturnOfAllMethod): MockObject|ArrayStorage
    {
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->once())->method('all')->willReturn($expectedReturnOfAllMethod);

        return $storageMock;
    }

    /**
     * @throws Exception
     */
    private function getCharger(
        CurrencyConverter|MockObject $currencyConverterMock,
        array                        $expectedReturnOfAllMethod,
        int                          $freeAmountPerWeek,
        int                          $freeCountPerWeek = 1
    ): PrivateClientWithdrawFeeCharger
    {
        return new PrivateClientWithdrawFeeCharger(
            $currencyConverterMock,
            $this->getStorageMock($expectedReturnOfAllMethod),
            0.1,
            $freeCountPerWeek,
            $freeAmountPerWeek,
            'EUR'
        );
    }
}
