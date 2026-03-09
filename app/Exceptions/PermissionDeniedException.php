<?php

namespace App\Exceptions;

class PermissionDeniedException extends BaseAppException
{
    public static function insufficientLevel(int $required, int $current): self
    {
        return new self(
            technicalMessage: "Insufficient accreditation level: required {$required}, current {$current}",
            userMessage: 'Vous n\'avez pas l\'accréditation nécessaire pour accéder à cette ressource.',
        );
    }

    public static function notDirectorG(): self
    {
        return new self(
            technicalMessage: 'User is not Director G',
            userMessage: 'Cet accès est réservé au Directeur G.',
        );
    }
}
