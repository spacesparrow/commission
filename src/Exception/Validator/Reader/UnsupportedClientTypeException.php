<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class UnsupportedClientTypeException extends \OutOfRangeException
{
    public const MESSAGE_PATTERN = 'Got unsupported client type: %s. Supported in config: %s';
}
