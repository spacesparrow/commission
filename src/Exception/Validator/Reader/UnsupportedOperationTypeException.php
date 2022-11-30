<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class UnsupportedOperationTypeException extends \OutOfBoundsException
{
    public const MESSAGE_PATTERN = 'Got unsupported operation type: %s. Supported in config: %s';
}
