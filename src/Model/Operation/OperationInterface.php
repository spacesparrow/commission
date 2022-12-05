<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Core\ModelInterface;

interface OperationInterface extends ModelInterface
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    public function getClient(): ClientInterface;

    public function setClient(ClientInterface $client): void;

    public function getProcessedAt(): \DateTimeInterface;

    public function setProcessedAt(\DateTimeInterface $processedAt): void;

    public function getAmount(): string;

    public function setAmount(string $amount): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getCurrency(): string;

    public function setCurrency(string $currency): void;
}
