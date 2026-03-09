<?php

namespace App\Exceptions;

class AgentNotFoundException extends BaseAppException
{
    public static function byId(int $id): self
    {
        return new self(
            technicalMessage: "Agent not found with ID {$id}",
            userMessage: 'L\'agent demandé est introuvable.',
        );
    }

    public static function byCode(string $code): self
    {
        return new self(
            technicalMessage: "Agent not found with code {$code}",
            userMessage: 'L\'agent demandé est introuvable.',
        );
    }
}
