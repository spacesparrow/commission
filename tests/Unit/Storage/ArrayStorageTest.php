<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Storage;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationTypeAwareInterface;
use App\CommissionTask\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class ArrayStorageTest extends TestCase
{
    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Storage\ArrayStorage::get
     */
    public function testGet(string $partition, $identifier, ?ModelInterface $model): void
    {
        $arrayStorage = new ArrayStorage();

        if ($model) {
            $arrayStorage->add($partition, $identifier, $model);
        }

        $got = $arrayStorage->get($partition, $identifier);

        static::assertEquals($model, $got);

        if ($model) {
            static::assertSame($identifier, $got->getIdentifier());
        }
    }

    /**
     * @dataProvider dataProviderForAllTesting
     * @covers       \App\CommissionTask\Storage\ArrayStorage::all
     */
    public function testAll(?string $partition, $identifier, $expected): void
    {
        $arrayStorage = new ArrayStorage();

        if (!empty($expected)) {
            $arrayStorage->add($partition, $identifier, $expected);
        }

        $got = $arrayStorage->all($partition);
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
     * @covers       \App\CommissionTask\Storage\ArrayStorage::has
     */
    public function testHas(string $partition, $identifier, ?ModelInterface $model): void
    {
        $arrayStorage = new ArrayStorage();

        if ($model) {
            $arrayStorage->add($partition, $identifier, $model);
        }

        static::assertSame($model !== null, $arrayStorage->has($partition, $identifier));
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Storage\ArrayStorage::add
     */
    public function testAdd(string $partition, $identifier, ?ModelInterface $model): void
    {
        $arrayStorage = new ArrayStorage();

        if ($model) {
            $arrayStorage->add($partition, $identifier, $model);
            $arrayStorage->add($partition, $identifier, $model);
        }

        static::assertSame($model !== null, $arrayStorage->has($partition, $identifier));
        static::assertEquals($model, $arrayStorage->get($partition, $identifier));
        static::assertCount($model ? 1 : 0, $arrayStorage->all($partition));
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Storage\ArrayStorage::remove
     */
    public function testRemove(string $partition, $identifier, ?ModelInterface $model): void
    {
        $arrayStorage = new ArrayStorage();

        if ($model) {
            $arrayStorage->add($partition, $identifier, $model);
        }

        static::assertSame($model !== null, $arrayStorage->has($partition, $identifier));
        static::assertEquals($model, $arrayStorage->get($partition, $identifier));
        static::assertCount($model ? 1 : 0, $arrayStorage->all($partition));

        if ($model) {
            $arrayStorage->remove($partition, $identifier);
        }

        static::assertFalse($arrayStorage->has($partition, $identifier));
        static::assertEmpty($arrayStorage->get($partition, $identifier));
        static::assertEmpty($arrayStorage->all($partition));
    }

    /**
     * @dataProvider dataProviderForAllTesting
     * @covers \App\CommissionTask\Storage\ArrayStorage::reset
     */
    public function testReset(?string $partition, $identifier, $expected): void
    {
        $arrayStorage = new ArrayStorage();

        if (!empty($expected)) {
            $arrayStorage->add($partition, $identifier, $expected);
        }

        $got = $arrayStorage->all($partition);
        static::assertIsArray($got);

        if (!empty($expected)) {
            static::assertNotEmpty($got);
            static::assertArrayHasKey($identifier, $got);
        } else {
            static::assertEmpty($got);
        }

        $arrayStorage->reset($partition);

        static::assertEmpty($arrayStorage->all($partition));
    }

    public function dataProviderGeneral(): array
    {
        $currencyFound = new Currency();
        $currencyFound->setCode('EUR');

        $clientFound = new Client();
        $clientFound->setId(1);

        $operationFound = new Operation();
        $operationFound->setProcessedAt(new \DateTime('2022-05-08 23:55:56'));
        $operationFound->setType(OperationTypeAwareInterface::TYPE_DEPOSIT);
        $operationFound->setClient($clientFound);

        return [
            ['currencies', 'EUR', $currencyFound],
            ['currencies', 'UBH', null],
            ['clients', 1, $clientFound],
            ['clients', -5, null],
            ['operations', '2022-05-08 23:55:561deposit', $operationFound],
            ['operations', '2022-05-09', null]
        ];
    }

    public function dataProviderForAllTesting(): array
    {
        return array_merge(
            $this->dataProviderGeneral(),
            [
                [null, 'EUR', []],
                [null, 1, []]
            ]
        );
    }
}
