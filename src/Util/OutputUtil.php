<?php

declare(strict_types=1);

namespace App\CommissionTask\Util;

class OutputUtil
{
    public static function writeLn($value): void
    {
        echo $value.PHP_EOL;
    }
}
