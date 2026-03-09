<?php

namespace App\Exceptions;

use RuntimeException;

abstract class BaseAppException extends RuntimeException
{
    protected string $userMessage;

    public function __construct(
        string $technicalMessage,
        string $userMessage,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->userMessage = $userMessage;
        parent::__construct($technicalMessage, $code, $previous);
    }

    /** Message destine a l'utilisateur (sans donnees techniques). */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
