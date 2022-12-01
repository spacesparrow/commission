<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Repository;

use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Repository\CurrencyRepository;
use App\CommissionTask\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class CurrencyRepositoryTest extends TestCase
{
    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\CurrencyRepository::get
     */
    public function testGet($identifier, ?ModelInterface $model): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if ($model) {
            $CurrencyRepository->add($model);
        }

        $got = $CurrencyRepository->get($identifier);

        static::assertEquals($model, $got);

        if ($model) {
            static::assertSame($identifier, $got->getIdentifier());
        }
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\CurrencyRepository::all
     */
    public function testAll($identifier, $expected): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if (!empty($expected)) {
            $CurrencyRepository->add($expected);
        }

        /** @var array $got */
        $got = $CurrencyRepository->all();
        static::assertIsArray($got);

        if (!empty($expected)) {
            static::assertNotEmpty($got);
            static::assertArrayHasKey($identifier, $got);
        } else {
            static::assertEmpty($got);
        }
    }


    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\CurrencyRepository::add
     */
    public function testAdd($identifier, ?ModelInterface $model): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if ($model) {
            $CurrencyRepository->add($model);
            $CurrencyRepository->add($model);
        }

        static::assertEquals($model, $CurrencyRepository->get($identifier));
        static::assertCount($model ? 1 : 0, $CurrencyRepository->all());
    }

    public function dataProviderGeneral(): array
    {
        $currencyFound = new Currency();
        $currencyFound->setCode('EUR');

        return [
            ['EUR', $currencyFound],
            ['UBH', null],
        ];
    }
}
