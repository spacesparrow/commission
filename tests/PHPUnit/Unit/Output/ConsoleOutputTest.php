<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\PHPUnit\Unit\Output;

use App\CommissionTask\Output\ConsoleOutput;
use PHPUnit\Framework\TestCase;

class ConsoleOutputTest extends TestCase
{
    public function testWriteLn(): void
    {
        $output = new ConsoleOutput();
        $this->expectOutputString('test'.PHP_EOL);
        $output->writeLn('test');
        $this->expectException(\TypeError::class);
        $output->writeLn(new ConsoleOutput());
    }
}
