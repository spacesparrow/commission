<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Validator\Reader;

class MissedSupportedClientTypesException extends \OutOfRangeException
{
    protected $message = 'Missing required config parameter: client types';
}
