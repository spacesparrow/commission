<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Factory;

class UnsupportedOperationCurrencyException extends \OutOfRangeException
{
    public const MESSAGE_PATTERN = 'Got undefined operation currency %s';
}
