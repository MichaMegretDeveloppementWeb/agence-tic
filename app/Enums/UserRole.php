<?php

namespace App\Enums;

enum UserRole: string
{
    case Agent = 'agent';
    case DirectorG = 'director_g';

    public function label(): string
    {
        return match ($this) {
            self::Agent => 'Agent',
            self::DirectorG => 'Directeur G',
        };
    }
}
