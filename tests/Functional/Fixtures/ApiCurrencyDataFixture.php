<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Functional\Fixtures;

class ApiCurrencyDataFixture
{
    /**
     * @throws \JsonException
     */
    public static function getTestData(): string
    {
        return json_encode([
            'base' => 'EUR',
            'date' => '2022-11-30',
            'rates' => [
                'EUR' => 1,
                'JPY' => 129.53,
                'USD' => 1.1497,
            ],
        ], JSON_THROW_ON_ERROR);
    }
}
