<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientType;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationType;
use App\CommissionTask\Storage\StorageInterface;

readonly class OperationFactory
{
    public function __construct(private StorageInterface $storage)
    {
    }

    /**
     * @throws \Exception
     */
    public function createFromCsvRow(array $csvRow): Operation
    {
        return new Operation(
            $csvRow['currency'],
            new \DateTime($csvRow['processed_at']),
            $csvRow['amount'],
            OperationType::from($csvRow['operation_type']),
            $this->getClient($csvRow['client_id'], $csvRow['client_type'])
        );
    }

    private function getClient(string $clientId, string $clientType): Client
    {
        $client = $this->storage->get(StorageInterface::PARTITION_CLIENTS, $clientId);

        if (!$client) {
            $client = new Client((int) $clientId, ClientType::from($clientType));
            $this->storage->add(StorageInterface::PARTITION_CLIENTS, $client->getIdentifier(), $client);
        }

        return $client;
    }
}
