<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Core\ModelInterface;

class Operation implements ModelInterface
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    public function __construct(
        private string $currency,
        private \DateTimeInterface $processedAt,
        private string $amount,
        private string $type,
        private Client $client
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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIdentifier(): string
    {
        return $this->getProcessedAt()->format('Y-m-d H:i:s').$this->getClient()->getId().$this->getType();
    }
}
