<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Provider;

use App\CommissionTask\Factory\Core\CurrencyFactory;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Provider\CurrencyProvider;
use App\CommissionTask\Repository\CurrencyRepository;
use PHPUnit\Framework\TestCase;

class CurrencyProviderTest extends TestCase
{
    /**
     * @dataProvider dataProviderForProviderTesting
     * @covers       \App\CommissionTask\Provider\CurrencyProvider::provide
     */
    public function testProvide($identifier, array $data, bool $found): void
    {
        $currencyRepositoryMock = $this->createMock(CurrencyRepository::class);
        $currencyFactoryMock = $this->createMock(CurrencyFactory::class);

        $currency = new Currency();
        $currency->setCode($identifier);
        $currency->setRate($data['rate']);
        $currency->setBase($data['base']);

        if ($found) {
            $currencyRepositoryMock
                ->expects(static::once())
                ->method('get')
                ->with($identifier)
                ->willReturn($currency);
            $currencyFactoryMock
                ->expects(static::never())
                ->method('createFromCodeAndRate')
                ->withAnyParameters();
        } else {
            $currencyRepositoryMock
                ->expects(static::once())
                ->method('get')
                ->with($identifier)
                ->willReturn(null);
            $currencyFactoryMock
                ->expects(static::once())
                ->method('createFromCodeAndRate')
                ->with($data['code'], $data['rate'], $data['base'])
                ->willReturn($currency);
        }

        $currencyRepositoryMock->expects(static::once())->method('add');

        $providedCurrency = (new CurrencyProvider($currencyRepositoryMock, $currencyFactoryMock))
            ->provide($identifier, $data);

        static::assertInstanceOf(ModelInterface::class, $providedCurrency);
        static::assertInstanceOf(CurrencyInterface::class, $providedCurrency);
        static::assertSame($data['code'], $providedCurrency->getCode());
        static::assertSame($identifier, $providedCurrency->getCode());
        static::assertSame($data['rate'], $providedCurrency->getRate());
        static::assertSame($data['base'], $providedCurrency->isBase());
        static::assertSame($data['code'], $providedCurrency->getIdentifier());
        static::assertSame($identifier, $providedCurrency->getIdentifier());
    }

    public function dataProviderForProviderTesting(): array
    {
        return [
            ['EUR', ['code' => 'EUR', 'rate' => '1', 'base' => true], true],
            ['UAH', ['code' => 'UAH', 'rate' => '41.15', 'base' => false], false],
            ['USD', ['code' => 'USD', 'rate' => '1.12', 'base' => false], true],
            ['JPY', ['code' => 'JPY', 'rate' => '129.18', 'base' => true], false]
        ];
    }
}
