<?php

declare(strict_types=1);

namespace App\CommissionTask\Util;

class DateTimeUtil
{
    public static function getWeekIdentifier(\DateTimeInterface $date): string
    {
        $dayOfWeek = $date->format('w');
        $date->modify('- '.(($dayOfWeek - 1 + 7) % 7).'days');
        $sunday = clone $date;
        $sunday->modify('+ 6 days');

        return "{$date->format('Y-m-d')}-{$sunday->format('Y-m-d')}";
    }
}
