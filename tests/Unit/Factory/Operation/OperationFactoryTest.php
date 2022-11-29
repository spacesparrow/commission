<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Factory\Operation;

use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientAwareInterface;
use App\CommissionTask\Model\Client\ClientTypeAwareInterface;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Model\Core\Partials\CurrencyAwareInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Model\Operation\OperationTypeAwareInterface;
use App\CommissionTask\Provider\ClientProvider;
use App\CommissionTask\Provider\CurrencyProvider;
use PHPUnit\Framework\TestCase;

class OperationFactoryTest extends TestCase
{
    /**
     * @covers \App\CommissionTask\Factory\Operation\OperationFactory::createNew
     */
    public function testCreateNew(): void
    {
        $clientProviderMock = $this->createMock(ClientProvider::class);
        $currencyProviderMock = $this->createMock(CurrencyProvider::class);
        $createdOperation = (new OperationFactory($currencyProviderMock, $clientProviderMock))->createNew();

        $this->assertInstances($createdOperation);
    }

    /**
     * @dataProvider dataProviderForCreateFromCsvRowTesting
     * @covers       \App\CommissionTask\Factory\Operation\OperationFactory::createFromCsvRow
     *
     * @throws \Exception
     */
    public function testCreateFromCsvRow(array $csvRow): void
    {
        $client = new Client();
        $client->setId($csvRow['client_id']);
        $client->setType($csvRow['client_type']);
        $currency = new Currency();
        $currency->setCode($csvRow['currency']);
        $currency->setRate('1.5');
        $currency->setBase(false);

        $clientProviderMock = $this->createMock(ClientProvider::class);
        $currencyProviderMock = $this->createMock(CurrencyProvider::class);

        $clientProviderMock
            ->expects(static::once())
            ->method('provide')
            ->with($csvRow['client_id'], ['client_type' => $csvRow['client_type']])
            ->willReturn($client);
        $currencyProviderMock
            ->expects(static::once())
            ->method('provide')
            ->with($csvRow['currency'], ['code' => $csvRow['currency'], 'rate' => '0', 'base' => false])
            ->willReturn($currency);

        $createdOperation = (new OperationFactory($currencyProviderMock, $clientProviderMock))
            ->createFromCsvRow($csvRow);

        $this->assertInstances($createdOperation);
        static::assertSame($csvRow['processed_at'], $createdOperation->getProcessedAt()->format('Y-m-d'));
        static::assertSame($csvRow['operation_type'], $createdOperation->getType());
        static::assertSame($csvRow['amount'], $createdOperation->getAmount());
        static::assertSame($csvRow['client_id'], $createdOperation->getClient()->getId());
        static::assertSame($csvRow['client_type'], $createdOperation->getClient()->getType());
        static::assertSame($csvRow['currency'], $createdOperation->getCurrency()->getCode());
    }

    public function dataProviderForCreateFromCsvRowTesting(): array
    {
        return [
            [
                [
                    'operation_type' => OperationTypeAwareInterface::TYPE_DEPOSIT,
                    'processed_at' => '2022-11-29',
                    'client_id' => 1,
                    'client_type' => ClientTypeAwareInterface::TYPE_PRIVATE,
                    'currency' => 'EUR',
                    'amount' => '100.50'
                ]
            ],
            [
                [
                    'operation_type' => OperationTypeAwareInterface::TYPE_WITHDRAW,
                    'processed_at' => '2022-11-30',
                    'client_id' => 1,
                    'client_type' => ClientTypeAwareInterface::TYPE_PRIVATE,
                    'currency' => 'USD',
                    'amount' => '250.50'
                ]
            ],
            [
                [
                    'operation_type' => OperationTypeAwareInterface::TYPE_DEPOSIT,
                    'processed_at' => '2022-12-01',
                    'client_id' => 2,
                    'client_type' => ClientTypeAwareInterface::TYPE_BUSINESS,
                    'currency' => 'UAH',
                    'amount' => '15675.35'
                ]
            ],
            [
                [
                    'operation_type' => OperationTypeAwareInterface::TYPE_WITHDRAW,
                    'processed_at' => '2022-12-02',
                    'client_id' => 2,
                    'client_type' => ClientTypeAwareInterface::TYPE_BUSINESS,
                    'currency' => 'GBP',
                    'amount' => '5.50'
                ]
            ]
        ];
    }

    private function assertInstances(OperationInterface $createdOperation): void
    {
        static::assertInstanceOf(OperationInterface::class, $createdOperation);
        static::assertInstanceOf(OperationTypeAwareInterface::class, $createdOperation);
        static::assertInstanceOf(CurrencyAwareInterface::class, $createdOperation);
        static::assertInstanceOf(ClientAwareInterface::class, $createdOperation);
        static::assertInstanceOf(ModelInterface::class, $createdOperation);
    }
}
