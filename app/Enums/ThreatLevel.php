<?php

namespace App\Enums;

enum ThreatLevel: string
{
    case Low = 'low';
    case Moderate = 'moderate';
    case High = 'high';
    case Critical = 'critical';
    case Extreme = 'extreme';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Faible',
            self::Moderate => 'Modéré',
            self::High => 'Élevé',
            self::Critical => 'Critique',
            self::Extreme => 'Extrême',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Moderate => 'blue',
            self::High => 'amber',
            self::Critical => 'red',
            self::Extreme => 'red',
        };
    }
}
