<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

use App\CommissionTask\Model\Core\Partials\TypeAwareInterface;

interface OperationTypeAwareInterface extends TypeAwareInterface
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';
}
