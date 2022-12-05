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

    public function getProcessedAt(): \DateTimeInterface;

    public function getAmount(): string;

    public function getType(): string;

    public function getCurrency(): string;
}
