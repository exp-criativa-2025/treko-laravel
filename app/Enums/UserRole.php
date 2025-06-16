<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case REPRESENTATIVE = 'representative';
    case DONOR = 'donor';

    public static function values(): array
    {
        // Return array of string values
        return array_map(fn($case) => $case->value, self::cases());
    }
}
