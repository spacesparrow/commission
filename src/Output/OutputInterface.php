<?php

declare(strict_types=1);

namespace App\CommissionTask\Output;

interface OutputInterface
{
    public function writeLn(\Stringable|string $value): void;
}
