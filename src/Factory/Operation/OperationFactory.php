<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Operation;

use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Provider\ProviderInterface;

class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        protected ProviderInterface $currencyProvider,
        protected ProviderInterface $clientProvider
    ) {
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
        /** @var ClientInterface $client */
        $client = $this->clientProvider->provide($csvRow['client_id'], ['client_type' => $csvRow['client_type']]);
        $operation->setClient($client);
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyProvider->provide(
            $csvRow['currency'],
            ['code' => $csvRow['currency'], 'rate' => '0', 'base' => false]
        );
        $operation->setCurrency($currency);

        return $operation;
    }
}
