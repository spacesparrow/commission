<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Operation;

enum OperationType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
