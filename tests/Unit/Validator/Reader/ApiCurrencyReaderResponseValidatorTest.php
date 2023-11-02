<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\BaseCurrencyMisconfigurationException;
use App\CommissionTask\Exception\Validator\Reader\SupportedCurrencyMisconfigurationException;
use App\CommissionTask\Validator\Reader\ApiCurrencyReaderResponseValidator;
use PHPUnit\Framework\TestCase;

class ApiCurrencyReaderResponseValidatorTest extends TestCase
{
    public function testValidateThrowsBaseCurrencyMisconfigurationException(): void
    {
        $validator = new ApiCurrencyReaderResponseValidator([], 'UAH');
        $this->expectException(BaseCurrencyMisconfigurationException::class);
        $this->expectExceptionMessage('Expected base currency UAH, got USD.');
        $validator->validate(['base' => 'USD']);
    }

    public function testValidateThrowsSupportedCurrencyMisconfigurationException(): void
    {
        $validator = new ApiCurrencyReaderResponseValidator([['code' => 'USD'], ['code' => 'UAH']], 'UAH');
        $this->expectException(SupportedCurrencyMisconfigurationException::class);
        $this->expectExceptionMessage('Missing rate for supported currency USD');
        $validator->validate(['base' => 'UAH', 'rates' => ['JPY' => 0.05]]);
    }

    public function testValidate(): void
    {
        $validator = new ApiCurrencyReaderResponseValidator(['code' => 'USD'], 'UAH');
        $this->expectNotToPerformAssertions();
        $validator->validate(['base' => 'UAH', 'rates' => ['USD' => 0.05]]);
    }
}
