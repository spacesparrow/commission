<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Kernel;

class UndefinedInstanceException extends \OutOfRangeException
{
    protected $message = 'Requested service was not found in the container.';
}
