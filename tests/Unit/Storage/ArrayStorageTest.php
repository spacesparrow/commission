<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Storage;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientType;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class ArrayStorageTest extends TestCase
{
    public function testWholeClass(): void
    {
        $storage = new ArrayStorage();
        $internalStorage = $storage->all();
        $this->assertEmpty($internalStorage);
        $this->assertArrayNotHasKey(StorageInterface::PARTITION_OPERATIONS, $internalStorage);
        $this->assertArrayNotHasKey(StorageInterface::PARTITION_CLIENTS, $internalStorage);
        $this->assertArrayNotHasKey(StorageInterface::PARTITION_CURRENCIES, $internalStorage);

        $storage->init();
        $internalStorage = $storage->all();
        $this->assertNotEmpty($internalStorage);
        $this->assertArrayHasKey(StorageInterface::PARTITION_OPERATIONS, $internalStorage);
        $this->assertEmpty($storage->all(StorageInterface::PARTITION_OPERATIONS));
        $this->assertArrayHasKey(StorageInterface::PARTITION_CLIENTS, $internalStorage);
        $this->assertEmpty($storage->all(StorageInterface::PARTITION_CLIENTS));
        $this->assertArrayHasKey(StorageInterface::PARTITION_CURRENCIES, $internalStorage);
        $this->assertEmpty($storage->all(StorageInterface::PARTITION_CURRENCIES));

        $this->assertNull($storage->get(StorageInterface::PARTITION_CLIENTS, '1'));
        $storage->add(StorageInterface::PARTITION_CLIENTS, '1', new Client(1, ClientType::PRIVATE));
        $this->assertNotNull($storage->get(StorageInterface::PARTITION_CLIENTS, '1'));
        $this->assertInstanceOf(Client::class, $storage->get(StorageInterface::PARTITION_CLIENTS, '1'));
        $this->assertCount(1, $storage->all(StorageInterface::PARTITION_CLIENTS));

        $this->assertTrue($storage->has(StorageInterface::PARTITION_CLIENTS, '1'));
        $storage->add(StorageInterface::PARTITION_CLIENTS, '1', new Client(1, ClientType::PRIVATE));
        $this->assertNotNull($storage->get(StorageInterface::PARTITION_CLIENTS, '1'));
        $this->assertInstanceOf(Client::class, $storage->get(StorageInterface::PARTITION_CLIENTS, '1'));
        $this->assertCount(1, $storage->all(StorageInterface::PARTITION_CLIENTS));
    }
}
