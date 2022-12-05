<?php

declare(strict_types=1);

namespace App\CommissionTask\Output;

class ConsoleOutput implements OutputInterface
{
    public function writeLn(\Stringable|string $value): void
    {
        echo $value.PHP_EOL;
    }
}
