<?php

namespace App\Exceptions;

class ReportNotFoundException extends BaseAppException
{
    public static function byId(int $id): self
    {
        return new self(
            technicalMessage: "Report not found with ID {$id}",
            userMessage: 'Le rapport demandé est introuvable.',
        );
    }
}
