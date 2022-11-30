<?php

declare(strict_types=1);

namespace App\CommissionTask\Exception\Reader;

class CommunicationException extends \RuntimeException
{
    protected $message = 'Unable to retrieve currencies data.';
}
