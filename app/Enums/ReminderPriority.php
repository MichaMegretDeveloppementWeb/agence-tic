<?php

namespace App\Enums;

enum ReminderPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Basse',
            self::Normal => 'Normale',
            self::High => 'Haute',
            self::Urgent => 'Urgente',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Normal => 'blue',
            self::High => 'amber',
            self::Urgent => 'red',
        };
    }
}
