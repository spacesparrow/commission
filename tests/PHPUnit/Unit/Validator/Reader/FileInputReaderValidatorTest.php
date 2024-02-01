<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\PHPUnit\Unit\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\UnsupportedClientTypeException;
use App\CommissionTask\Exception\Validator\Reader\UnsupportedOperationTypeException;
use App\CommissionTask\Validator\Reader\FileInputReaderValidator;
use PHPUnit\Framework\TestCase;

class FileInputReaderValidatorTest extends TestCase
{
    public function testValidateThrowsUnsupportedClientTypeException(): void
    {
        $validator = new FileInputReaderValidator(['private'], []);
        $this->expectException(UnsupportedClientTypeException::class);
        $this->expectExceptionMessage('Got unsupported client type: business. Supported in config: private');
        $validator->validate(['client_type' => 'business']);
    }

    public function testValidateThrowsUnsupportedOperationTypeException(): void
    {
        $validator = new FileInputReaderValidator(['private'], ['deposit']);
        $this->expectException(UnsupportedOperationTypeException::class);
        $this->expectExceptionMessage('Got unsupported operation type: withdraw. Supported in config: deposit');
        $validator->validate(['client_type' => 'private', 'operation_type' => 'withdraw']);
    }

    public function testValidate(): void
    {
        $validator = new FileInputReaderValidator(['private'], ['deposit']);
        $this->expectNotToPerformAssertions();
        $validator->validate(['client_type' => 'private', 'operation_type' => 'deposit']);
    }
}
