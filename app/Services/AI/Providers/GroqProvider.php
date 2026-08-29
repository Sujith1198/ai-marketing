<?php

namespace App\Services\AI\Providers;

use App\Models\AIProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\Security\SecureVaultService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqProvider implements AIProviderInterface
{
    protected AIProvider $provider;
    protected SecureVaultService $vault;

    public function __construct(AIProvider $provider)
    {
        $this->provider = $provider;
        $this->vault = app(SecureVaultService::class);
    }

    public function generateText(string $prompt, array $options = []): string
    {
        $apiKey = $this->getApiKey();
        $model = $options['model'] ?? $this->provider->default_model ?? 'llama-3.3-70b-versatile';

        if (empty($apiKey)) {
            return "[Groq Provider Notice]: API key missing. (Manual Mode)";
        }

        try {
            $response = Http::withToken($apiKey)->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 2048,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? '';
            }

            Log::error("Groq API Error", ['status' => $response->status(), 'body' => $response->body()]);
            return "[Groq Error]: " . $response->status();
        } catch (\Exception $e) {
            Log::error("Groq Exception: " . $e->getMessage());
            return "[Groq Exception]: " . $e->getMessage();
        }
    }

    public function generateStructuredOutput(string $prompt, array $jsonSchema = [], array $options = []): array
    {
        $jsonPrompt = $prompt . "\n\nReturn JSON output only.";
        $rawOutput = $this->generateText($jsonPrompt, $options);
        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawOutput));
        $decoded = json_decode($cleanJson, true);

        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : ['error' => 'Invalid JSON', 'raw' => $rawOutput];
    }

    public function testConnection(): bool
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) return false;

        $response = Http::withToken($apiKey)->timeout(10)->get('https://api.groq.com/openai/v1/models');
        return $response->successful();
    }

    public function getAvailableModels(): array
    {
        return [
            'llama-3.3-70b-versatile' => 'Llama 3.3 70B (High Quality)',
            'llama-3.1-8b-instant' => 'Llama 3.1 8B (Ultra Fast)',
            'mixtral-8x7b-32768' => 'Mixtral 8x7B (Large Context)',
        ];
    }

    public function estimateUsage(string $prompt): int
    {
        return (int) ceil(strlen($prompt) / 4);
    }

    protected function getApiKey(): ?string
    {
        if ($this->provider->credential) {
            return $this->vault->getDecryptedSecret($this->provider->credential);
        }
        return env('GROQ_API_KEY');
    }
}
