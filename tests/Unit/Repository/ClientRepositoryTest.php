<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Repository;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Repository\ClientRepository;
use App\CommissionTask\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{
    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\ClientRepository::get
     */
    public function testGet($identifier, ?ModelInterface $model): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if ($model) {
            $clientRepository->add($model);
        }

        $got = $clientRepository->get($identifier);

        static::assertEquals($model, $got);

        if ($model) {
            static::assertSame($identifier, $got->getIdentifier());
        }
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\ClientRepository::all
     */
    public function testAll($identifier, $expected): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if (!empty($expected)) {
            $clientRepository->add($expected);
        }

        /** @var array $got */
        $got = $clientRepository->all();
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
     * @covers       \App\CommissionTask\Repository\ClientRepository::has
     */
    public function testHas($identifier, ?ModelInterface $model): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if ($model) {
            $clientRepository->add($model);
        }

        static::assertSame($model !== null, $clientRepository->has($identifier));
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\ClientRepository::add
     */
    public function testAdd($identifier, ?ModelInterface $model): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if ($model) {
            $clientRepository->add($model);
            $clientRepository->add($model);
        }

        static::assertSame($model !== null, $clientRepository->has($identifier));
        static::assertEquals($model, $clientRepository->get($identifier));
        static::assertCount($model ? 1 : 0, $clientRepository->all());
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers       \App\CommissionTask\Repository\ClientRepository::remove
     */
    public function testRemove($identifier, ?ModelInterface $model): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if ($model) {
            $clientRepository->add($model);
        }

        static::assertSame($model !== null, $clientRepository->has($identifier));
        static::assertEquals($model, $clientRepository->get($identifier));
        static::assertCount($model ? 1 : 0, $clientRepository->all());

        if ($model) {
            $clientRepository->remove($identifier);
        }

        static::assertFalse($clientRepository->has($identifier));
        static::assertEmpty($clientRepository->get($identifier));
        static::assertEmpty($clientRepository->all());
    }

    /**
     * @dataProvider dataProviderGeneral
     * @covers \App\CommissionTask\Storage\ArrayStorage::reset
     */
    public function testReset($identifier, $model): void
    {
        $clientRepository = new ClientRepository(new ArrayStorage());

        if (!empty($model)) {
            $clientRepository->add($model);
        }

        /** @var array $got */
        $got = $clientRepository->all();
        static::assertIsArray($got);

        if (!empty($model)) {
            static::assertNotEmpty($got);
            static::assertArrayHasKey($identifier, $got);
        } else {
            static::assertEmpty($got);
        }

        $clientRepository->reset();

        static::assertEmpty($clientRepository->all());
    }

    public function dataProviderGeneral(): array
    {
        $clientFound = new Client();
        $clientFound->setId(1);

        return [
            ['1', $clientFound],
            ['-5', null]
        ];
    }
}
