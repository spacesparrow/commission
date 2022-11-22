<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Factory\Client\ClientFactoryInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationInterface;

class OperationFactory implements OperationFactoryInterface
{
    private ClientFactoryInterface $clientFactory;

    public function __construct(ClientFactoryInterface $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    public function createNew(): OperationInterface
    {
        return new Operation();
    }

    /**
     * @throws \Exception
     */
    public function createFromCsvRow(array $csvRow): OperationInterface
    {
        $operation = $this->createNew();
        $operation->setType($csvRow['operation_type']);
        $operation->setProcessedAt(new \DateTime($csvRow['processed_at']));
        $operation->setAmount($csvRow['amount']);
        $operation->setClient($this->clientFactory->createFromIdAndType($csvRow['client_id'], $csvRow['client_type']));

        return $operation;
    }
}
