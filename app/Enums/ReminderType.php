<?php

namespace App\Enums;

enum ReminderType: string
{
    case Personal = 'personal';
    case Targeted = 'targeted';
    case Global = 'global';

    public function label(): string
    {
        return match ($this) {
            self::Personal => 'Personnel',
            self::Targeted => 'Ciblé',
            self::Global => 'Global',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Personal => 'gray',
            self::Targeted => 'blue',
            self::Global => 'indigo',
        };
    }
}
