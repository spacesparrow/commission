<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Factory\Core;

use App\CommissionTask\Factory\Core\CurrencyFactory;
use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use PHPUnit\Framework\TestCase;

class CurrencyFactoryTest extends TestCase
{
    /**
     * @covers \App\CommissionTask\Factory\Core\CurrencyFactory::createNew
     */
    public function testCreateNew(): void
    {
        $createdCurrency = (new CurrencyFactory())->createNew();

        static::assertInstanceOf(CurrencyInterface::class, $createdCurrency);
        static::assertInstanceOf(ModelInterface::class, $createdCurrency);
    }

    /**
     * @dataProvider dataProviderForCreateFromCodeAndRateTesting
     * @covers       \App\CommissionTask\Factory\Core\CurrencyFactory::createFromCodeAndRate
     */
    public function testCreateFromCodeAndRate(string $code, string $rate, bool $base): void
    {
        $createdCurrency = (new CurrencyFactory())->createFromCodeAndRate($code, $rate, $base);

        static::assertInstanceOf(CurrencyInterface::class, $createdCurrency);
        static::assertInstanceOf(ModelInterface::class, $createdCurrency);
        static::assertSame($code, $createdCurrency->getCode());
        static::assertSame($rate, $createdCurrency->getRate());
        static::assertSame($base, $createdCurrency->isBase());
        static::assertSame($code, $createdCurrency->getIdentifier());
    }

    public function dataProviderForCreateFromCodeAndRateTesting(): array
    {
        return [
            ['EUR', '1', true],
            ['UAH', '41.15', false],
            ['GBP', '0.78', false],
            ['JPY', '129.18', false]
        ];
    }
}
