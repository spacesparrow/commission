<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Converter;

class TooManyBaseCurrenciesException extends \InvalidArgumentException
{
    protected $message = 'Found more than 1 base currency. Cannot convert.';
}
