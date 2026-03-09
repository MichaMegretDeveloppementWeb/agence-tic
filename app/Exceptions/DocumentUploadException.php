<?php

namespace App\Exceptions;

class DocumentUploadException extends BaseAppException
{
    public static function storageFailed(string $filename): self
    {
        return new self(
            technicalMessage: "Failed to store uploaded file: {$filename}",
            userMessage: 'Impossible de téléverser le fichier. Veuillez réessayer. Si le problème persiste, contactez le support.',
        );
    }

    public static function fileNotFound(string $path): self
    {
        return new self(
            technicalMessage: "File not found at path: {$path}",
            userMessage: 'Le fichier demandé est introuvable sur le serveur.',
        );
    }
}
