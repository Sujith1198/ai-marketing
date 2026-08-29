<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    public function generateText(string $prompt, array $options = []): string;
    public function generateStructuredOutput(string $prompt, array $jsonSchema = [], array $options = []): array;
    public function testConnection(): bool;
    public function getAvailableModels(): array;
    public function estimateUsage(string $prompt): int;
}
