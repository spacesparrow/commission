<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(protected RepositoryInterface $clientRepository)
    {
    }

    /**
     * @throws \Exception
     */
    public function createFromCsvRow(array $csvRow): OperationInterface
    {
        $operation = new Operation();
        $operation->setType($csvRow['operation_type']);
        $operation->setProcessedAt(new \DateTime($csvRow['processed_at']));
        $operation->setAmount($csvRow['amount']);
        $operation->setClient($this->getClient($csvRow['client_id'], $csvRow['client_type']));
        $operation->setCurrency($csvRow['currency']);

        return $operation;
    }

    private function getClient(string $clientId, string $clientType): ClientInterface
    {
        $client = $this->clientRepository->get($clientId);

        if (!$client) {
            $client = new Client();
            $client->setId((int) $clientId);
            $client->setType($clientType);
            $this->clientRepository->add($client);
        }

        return $client;
    }
}
