<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Client\ClientInterface;

class Operation implements OperationInterface
{
    public function __construct(
        private string $currency,
        private \DateTimeInterface $processedAt,
        private string $amount,
        private string $type,
        private ClientInterface $client
    ) {
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getProcessedAt(): \DateTimeInterface
    {
        return $this->processedAt;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function getIdentifier(): string
    {
        return $this->getProcessedAt()->format('Y-m-d H:i:s').$this->getClient()->getId().$this->getType();
    }
}
