<?php

namespace App\DTOs;

class ProviderConnectionResultDTO
{
    public function __construct(
        public bool $success,
        public string $mode, // 'api' or 'manual'
        public string $message,
        public array $details = []
    ) {}

    public static function apiSuccess(string $message = 'Connected via API successfully', array $details = []): self
    {
        return new self(true, 'api', $message, $details);
    }

    public static function manualSuccess(string $message = 'Configured in Manual Mode', array $details = []): self
    {
        return new self(true, 'manual', $message, $details);
    }

    public static function failed(string $message, array $details = []): self
    {
        return new self(false, 'api', $message, $details);
    }
}
