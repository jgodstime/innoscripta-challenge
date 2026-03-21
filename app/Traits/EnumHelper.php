<?php

namespace App\Traits;

trait EnumHelper
{
    public static function toArray(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
