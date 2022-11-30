<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Repository;

use App\CommissionTask\Model\Client\Client;
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
     * @covers       \App\CommissionTask\Repository\CurrencyRepository::has
     */
    public function testHas($identifier, ?ModelInterface $model): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if ($model) {
            $CurrencyRepository->add($model);
        }

        static::assertSame($model !== null, $CurrencyRepository->has($identifier));
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

        static::assertSame($model !== null, $CurrencyRepository->has($identifier));
        static::assertEquals($model, $CurrencyRepository->get($identifier));
        static::assertCount($model ? 1 : 0, $CurrencyRepository->all());
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\CurrencyRepository::remove
     */
    public function testRemove($identifier, ?ModelInterface $model): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if ($model) {
            $CurrencyRepository->add($model);
        }

        static::assertSame($model !== null, $CurrencyRepository->has($identifier));
        static::assertEquals($model, $CurrencyRepository->get($identifier));
        static::assertCount($model ? 1 : 0, $CurrencyRepository->all());

        if ($model) {
            $CurrencyRepository->remove($identifier);
        }

        static::assertFalse($CurrencyRepository->has($identifier));
        static::assertEmpty($CurrencyRepository->get($identifier));
        static::assertEmpty($CurrencyRepository->all());
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Storage\ArrayStorage::reset
     */
    public function testReset($identifier, $model): void
    {
        $CurrencyRepository = new CurrencyRepository(new ArrayStorage());

        if (!empty($model)) {
            $CurrencyRepository->add($model);
        }

        /** @var array $got */
        $got = $CurrencyRepository->all();
        static::assertIsArray($got);

        if (!empty($model)) {
            static::assertNotEmpty($got);
            static::assertArrayHasKey($identifier, $got);
        } else {
            static::assertEmpty($got);
        }

        $CurrencyRepository->reset();

        static::assertEmpty($CurrencyRepository->all());
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
