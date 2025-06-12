<?php

namespace App\Enums;

enum AcademicEntityType
{
    case ACADEMIC_CENTER;
    case CENTRAL_DIRECTORY;
    case ATHLETIC_ASSOCIATION;
    case CLUB;
    case BATTERY;

    public function label(): string
    {
        return match ($this) {
            self::ACADEMIC_CENTER => 'Academic Center',
            self::CENTRAL_DIRECTORY => 'Central Directory',
            self::ATHLETIC_ASSOCIATION => 'Athletic Association',
            self::CLUB => 'Club',
            self::BATTERY => 'Battery',
        };
    }
}
