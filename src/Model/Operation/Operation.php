<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Core\CurrencyInterface;

class Operation implements OperationInterface
{
    protected CurrencyInterface $currency;

    protected \DateTimeInterface $processedAt;

    protected string $amount;

    protected string $type;

    private ClientInterface  $client;

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function getProcessedAt(): \DateTimeInterface
    {
        return $this->processedAt;
    }

    public function setProcessedAt(\DateTimeInterface $processedAt): void
    {
        $this->processedAt = $processedAt;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    public function getIdentifier(): string
    {
        return $this->getProcessedAt()->format('Y-m-d H:i:s').$this->getClient()->getId().$this->getType();
    }
}
