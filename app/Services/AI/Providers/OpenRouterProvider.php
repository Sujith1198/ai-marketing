<?php

namespace App\Services\AI\Providers;

use App\Models\AIProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\Security\SecureVaultService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterProvider implements AIProviderInterface
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
        $model = $options['model'] ?? $this->provider->default_model ?? 'meta-llama/llama-3.1-70b-instruct:free';

        if (empty($apiKey)) {
            return "[OpenRouter Provider Notice]: API key missing. (Manual Mode)";
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
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

            Log::error("OpenRouter API Error", ['status' => $response->status(), 'body' => $response->body()]);
            return "[OpenRouter Error]: " . $response->status();
        } catch (\Exception $e) {
            Log::error("OpenRouter Exception: " . $e->getMessage());
            return "[OpenRouter Exception]: " . $e->getMessage();
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

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
        ])->timeout(10)->get('https://openrouter.ai/api/v1/models');
        return $response->successful();
    }

    public function getAvailableModels(): array
    {
        return [
            'meta-llama/llama-3.1-70b-instruct:free' => 'Llama 3.1 70B (Free tier)',
            'google/gemini-2.0-flash-exp:free' => 'Gemini 2.0 Flash (Free tier)',
            'deepseek/deepseek-r1' => 'DeepSeek R1',
            'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
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
        return env('OPENROUTER_API_KEY');
    }
}
