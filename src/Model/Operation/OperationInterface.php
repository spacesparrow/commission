<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Client\ClientAwareInterface;
use App\CommissionTask\Model\Core\Partials\CurrencyAwareInterface;

interface OperationInterface extends OperationTypeAwareInterface, CurrencyAwareInterface, ClientAwareInterface
{
    public function getProcessedAt(): \DateTimeInterface;

    public function setProcessedAt(\DateTimeInterface $processedAt): void;

    public function getAmount(): string;

    public function setAmount(string $amount): void;
}
