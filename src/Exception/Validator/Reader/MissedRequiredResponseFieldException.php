<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class MissedRequiredResponseFieldException extends \OutOfRangeException
{
    private const MESSAGE_PATTERN = 'Missing required response field: %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $message));
    }
}
