<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Storage\StorageInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(private StorageInterface $storage)
    {
    }

    /**
     * @throws \Exception
     */
    public function createFromCsvRow(array $csvRow): OperationInterface
    {
        return new Operation(
            currency: $csvRow['currency'],
            processedAt: new \DateTime($csvRow['processed_at']),
            amount: $csvRow['amount'],
            type: $csvRow['operation_type'],
            client: $this->getClient($csvRow['client_id'], $csvRow['client_type'])
        );
    }

    private function getClient(string $clientId, string $clientType): ClientInterface
    {
        $client = $this->storage->get(StorageInterface::PARTITION_CLIENTS, $clientId);

        if (!$client) {
            $client = new Client(id: (int) $clientId, type: $clientType);
            $this->storage->add(StorageInterface::PARTITION_CLIENTS, $client->getIdentifier(), $client);
        }

        return $client;
    }
}
