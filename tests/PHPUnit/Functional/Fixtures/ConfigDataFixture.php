<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\PHPUnit\Functional\Fixtures;

class ConfigDataFixture
{
    public static function getTestData(): array
    {
        return [
            'parameters' => [
                'reader' => [
                    'max_attempts' => 5,
                    'response_required_fields' => [
                        'base_currency_field' => 'base',
                        'rates_field' => 'rates',
                    ],
                ],
                'client' => [
                    'types' => [
                        0 => 'business',
                        1 => 'private',
                    ],
                ],
                'operation' => [
                    'types' => [
                        0 => 'deposit',
                        1 => 'withdraw',
                    ],
                ],
                'currency' => [
                    'base_currency_code' => 'EUR',
                    'supported' => [
                        0 => [
                            'code' => 'EUR',
                            'scale' => 2,
                        ],
                        1 => [
                            'code' => 'USD',
                            'scale' => 2,
                        ],
                        2 => [
                            'code' => 'JPY',
                            'scale' => 0,
                        ],
                    ],
                ],
                'fee' => [
                    'deposit' => [
                        'percent' => 0.0003,
                    ],
                    'withdraw' => [
                        'business' => [
                            'percent' => 0.005,
                        ],
                        'private' => [
                            'percent' => 0.003,
                            'free_amount_per_week' => 1000,
                            'free_count_per_week' => 3,
                        ],
                    ],
                ],
            ],
        ];
    }
}
