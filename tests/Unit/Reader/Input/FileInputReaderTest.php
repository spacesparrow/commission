<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Reader\Input;

use App\CommissionTask\Reader\Input\FileInputReader;
use App\CommissionTask\Validator\Reader\FileInputReaderValidator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class FileInputReaderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRead(): void
    {
        $validatorMock = $this->createMock(FileInputReaderValidator::class);
        $validatorMock->expects($this->once())->method('validate');
        $fileInputReader = new FileInputReader($validatorMock);
        $row = $fileInputReader->read('input.example.csv');
        $rowPlain = $row->current();
        $this->assertNotEmpty($rowPlain);
        $this->assertInstanceOf(\Generator::class, $row);
        $this->assertIsArray($rowPlain);
        $this->assertArrayHasKey('processed_at', $rowPlain);
        $this->assertArrayHasKey('client_id', $rowPlain);
        $this->assertArrayHasKey('client_type', $rowPlain);
        $this->assertArrayHasKey('operation_type', $rowPlain);
        $this->assertArrayHasKey('amount', $rowPlain);
        $this->assertArrayHasKey('currency', $rowPlain);
    }
}
