<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Active = 'active';
    case Contained = 'contained';
    case Neutralized = 'neutralized';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Actif',
            self::Contained => 'Confiné',
            self::Neutralized => 'Neutralisé',
            self::Archived => 'Archivé',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active => 'emerald',
            self::Contained => 'amber',
            self::Neutralized => 'blue',
            self::Archived => 'gray',
        };
    }
}
