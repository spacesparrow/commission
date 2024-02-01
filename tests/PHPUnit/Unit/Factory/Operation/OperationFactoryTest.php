<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\PHPUnit\Unit\Factory\Operation;

use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientType;
use App\CommissionTask\Model\Operation\OperationType;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Storage\StorageInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class OperationFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateFromCsvRowClientFound(): void
    {
        $client = new Client(1, ClientType::PRIVATE);
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->once())
            ->method('get')
            ->with(StorageInterface::PARTITION_CLIENTS, (string) $client->getId())
            ->willReturn($client);
        $storageMock->expects($this->never())->method('add');
        $csvRow = [
            'currency' => 'EUR',
            'processed_at' => '2023-10-31 23:59:59',
            'amount' => '5',
            'operation_type' => OperationType::DEPOSIT->value,
            'client_id' => (string) $client->getId(),
            'client_type' => $client->getType()->value,
        ];
        $operationFactory = new OperationFactory($storageMock);
        $operation = $operationFactory->createFromCsvRow($csvRow);
        $this->assertSame($csvRow['currency'], $operation->getCurrency());
        $this->assertEquals(new \DateTime($csvRow['processed_at']), $operation->getProcessedAt());
        $this->assertSame($csvRow['amount'], $operation->getAmount());
        $this->assertSame($csvRow['operation_type'], $operation->getType()->value);
        $this->assertEquals($client, $operation->getClient());
        $this->assertSame($csvRow['client_id'], (string) $operation->getClient()->getId());
        $this->assertSame($csvRow['client_type'], $operation->getClient()->getType()->value);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateFromCsvRowClientNotFound(): void
    {
        $client = new Client(1, ClientType::PRIVATE);
        $storageMock = $this->createMock(ArrayStorage::class);
        $storageMock->expects($this->once())
            ->method('get')
            ->with(StorageInterface::PARTITION_CLIENTS, (string) $client->getId())
            ->willReturn(null);
        $storageMock->expects($this->once())
            ->method('add')
            ->with(StorageInterface::PARTITION_CLIENTS, $client->getIdentifier(), $client);
        $csvRow = [
            'currency' => 'EUR',
            'processed_at' => '2023-10-31 23:59:59',
            'amount' => '5',
            'operation_type' => OperationType::DEPOSIT->value,
            'client_id' => (string) $client->getId(),
            'client_type' => $client->getType()->value,
        ];
        $operationFactory = new OperationFactory($storageMock);
        $operation = $operationFactory->createFromCsvRow($csvRow);
        $this->assertSame($csvRow['currency'], $operation->getCurrency());
        $this->assertEquals(new \DateTime($csvRow['processed_at']), $operation->getProcessedAt());
        $this->assertSame($csvRow['amount'], $operation->getAmount());
        $this->assertSame($csvRow['operation_type'], $operation->getType()->value);
        $this->assertEquals($client, $operation->getClient());
        $this->assertSame($csvRow['client_id'], (string) $operation->getClient()->getId());
        $this->assertSame($csvRow['client_type'], $operation->getClient()->getType()->value);
    }
}
