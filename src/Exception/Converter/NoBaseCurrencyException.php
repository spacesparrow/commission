<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Converter;

class NoBaseCurrencyException extends \OutOfBoundsException
{
    protected $message = 'Found no base currency. Cannot convert.';
}
