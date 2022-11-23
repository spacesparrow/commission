<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class MissedRequiredResponseFieldException extends \OutOfRangeException
{
    public const MESSAGE_PATTERN = 'Missing required response field: %s.';
}
