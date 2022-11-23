<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class BaseCurrencyMisconfigurationException extends \UnexpectedValueException
{
    public const MESSAGE_PATTERN = 'Expected base currency %s, got %s.';
}
