<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\PHPUnit\Unit\Converter;

use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Exception\Converter\NoBaseCurrencyException;
use App\CommissionTask\Exception\Converter\TooManyBaseCurrenciesException;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Storage\StorageInterface;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    public static function dataProviderForConvertEqualAmountTesting(): array
    {
        return [
            ['EUR', 'EUR', '5.03'],
            ['UAH', 'UAH', '100.1'],
            ['GBP', 'GBP', '0.05'],
            ['USD', 'USD', '0'],
        ];
    }

    /**
     * @throws CurrencyConversionException
     * @throws RoundingNecessaryException
     * @throws Exception
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    #[DataProvider('dataProviderForConvertEqualAmountTesting')]
    public function testConvertEqualAmount(string $from, string $to, string $amount): void
    {
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->never())->method('all');
        $currencyReaderMock = $this->createMock(ApiCurrencyReader::class);
        $currencyReaderMock->expects($this->never())->method('read');
        $converter = new CurrencyConverter($storageMock, $currencyReaderMock);
        $this->assertEquals(BigDecimal::of($amount), $converter->convert($from, $to, $amount));
    }

    /**
     * @throws CurrencyConversionException
     * @throws RoundingNecessaryException
     * @throws Exception
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public function testConvertThrowsNoBaseCurrenciesException(): void
    {
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->exactly(2))
            ->method('all')
            ->with(StorageInterface::PARTITION_CURRENCIES)
            ->willReturn([]);
        $currencyReaderMock = $this->createMock(ApiCurrencyReader::class);
        $currencyReaderMock->expects($this->once())->method('read');
        $converter = new CurrencyConverter($storageMock, $currencyReaderMock);
        $this->expectException(NoBaseCurrencyException::class);
        $converter->convert('USD', 'UAH', '5');
    }

    /**
     * @throws CurrencyConversionException
     * @throws RoundingNecessaryException
     * @throws Exception
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    public function testConvertThrowsTooManyBaseCurrenciesException(): void
    {
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->exactly(2))
            ->method('all')
            ->with(StorageInterface::PARTITION_CURRENCIES)
            ->willReturnOnConsecutiveCalls(
                [],
                [new Currency('', '', true), new Currency('', '', true)]
            );
        $currencyReaderMock = $this->createMock(ApiCurrencyReader::class);
        $currencyReaderMock->expects($this->once())->method('read');
        $converter = new CurrencyConverter($storageMock, $currencyReaderMock);
        $this->expectException(TooManyBaseCurrenciesException::class);
        $converter->convert('USD', 'UAH', '5');
    }

    /**
     * @throws MathException
     */
    public static function dataProviderForConvertTesting(): array
    {
        return [
            ['EUR', 'USD', '5', BigDecimal::of(10)->toScale(2, RoundingMode::UP)],
            ['EUR', 'USD', '0.5', BigDecimal::of(1)->toScale(2, RoundingMode::UP)],
            ['USD', 'EUR', '10', BigDecimal::of(5)->toScale(2, RoundingMode::UP)],
            ['USD', 'EUR', '5', BigDecimal::of(2.5)->toScale(2, RoundingMode::UP)],
        ];
    }

    /**
     * @throws CurrencyConversionException
     * @throws Exception
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    #[DataProvider('dataProviderForConvertTesting')]
    public function testConvert(string $from, string $to, string $amount, BigDecimal $expectedConvertedAmount): void
    {
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->exactly(4))
            ->method('all')
            ->with(StorageInterface::PARTITION_CURRENCIES)
            ->willReturn([new Currency('EUR', '1', true), new Currency('USD', '2', false)]);
        $currencyReaderMock = $this->createMock(ApiCurrencyReader::class);
        $currencyReaderMock->expects($this->never())->method('read');
        $converter = new CurrencyConverter($storageMock, $currencyReaderMock);
        $this->assertEquals($expectedConvertedAmount, $converter->convert($from, $to, $amount));
    }
}
