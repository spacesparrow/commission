<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Reader;

class InvalidDataException extends \InvalidArgumentException
{
    protected $message = 'Received unserializable currencies data.';
}
