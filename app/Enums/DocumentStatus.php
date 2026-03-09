<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Active = 'active';
    case Archived = 'archived';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Archived => 'Archivé',
            self::Hidden => 'Masqué',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active => 'emerald',
            self::Archived => 'gray',
            self::Hidden => 'amber',
        };
    }
}
