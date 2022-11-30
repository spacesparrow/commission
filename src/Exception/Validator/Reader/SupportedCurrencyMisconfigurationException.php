<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class SupportedCurrencyMisconfigurationException extends \OutOfRangeException
{
    public const MESSAGE_PATTERN = 'Missing rate for supported currency %s.';
}
