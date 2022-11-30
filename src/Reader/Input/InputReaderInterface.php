<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Input;

interface InputReaderInterface
{
    public function read($source);
}
