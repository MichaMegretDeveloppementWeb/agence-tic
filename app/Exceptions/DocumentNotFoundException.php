<?php

namespace App\Exceptions;

class DocumentNotFoundException extends BaseAppException
{
    public static function byId(int $id): self
    {
        return new self(
            technicalMessage: "Document not found with ID {$id}",
            userMessage: 'Le document demandé est introuvable.',
        );
    }
}
