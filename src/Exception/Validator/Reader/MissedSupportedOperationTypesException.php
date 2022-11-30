<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class MissedSupportedOperationTypesException extends \OutOfRangeException
{
    protected $message = 'Missing required config parameter: operation types';
}
