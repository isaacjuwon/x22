<?php

namespace App\Enums;

enum DateRangePreset: string
{
    case Today = 'today';
    case ThisWeek = 'this_week';
    case ThisMonth = 'this_month';
    case LastMonth = 'last_month';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forJs(): array
    {
        return self::all();
    }
}