<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Util;

use App\CommissionTask\Util\DateTimeUtil;
use PHPUnit\Framework\TestCase;

class DateTimeUtilTest extends TestCase
{
    /**
     * @covers \App\CommissionTask\Util\DateTimeUtil::getWeekIdentifier
     * @dataProvider dataProviderForGetWeekIdentifierTesting
     */
    public function testGetWeekIdentifier(\DateTimeInterface $date, string $expectedResult): void
    {
        $weekIdentifier = DateTimeUtil::getWeekIdentifier($date);

        static::assertIsString($expectedResult);
        static::assertSame($expectedResult, $weekIdentifier);
    }

    public function dataProviderForGetWeekIdentifierTesting(): array
    {
        return [
            [
                new \DateTime('2022-11-29'),
                '2022-11-28-2022-12-04'
            ],
            [
                new \DateTime('2022-04-14'),
                '2022-04-11-2022-04-17'
            ],
            [
                new \DateTime('2020-01-01'),
                '2019-12-30-2020-01-05'
            ],
            [
                new \DateTime('2023-01-11'),
                '2023-01-09-2023-01-15'
            ]
        ];
    }
}
