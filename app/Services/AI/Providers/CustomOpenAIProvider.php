<?php

namespace App\Services\AI\Providers;

use App\Models\AIProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\Security\SecureVaultService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomOpenAIProvider implements AIProviderInterface
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
        $endpoint = $this->provider->api_endpoint ?? 'https://api.openai.com/v1/chat/completions';
        $model = $options['model'] ?? $this->provider->default_model ?? 'gpt-4o-mini';

        if (empty($apiKey)) {
            return "[Custom OpenAI Provider Notice]: API key missing. (Manual Mode)";
        }

        try {
            $response = Http::withToken($apiKey)->timeout(30)->post($endpoint, [
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

            Log::error("Custom OpenAI API Error", ['status' => $response->status(), 'body' => $response->body()]);
            return "[Custom OpenAI Error]: " . $response->status();
        } catch (\Exception $e) {
            Log::error("Custom OpenAI Exception: " . $e->getMessage());
            return "[Custom OpenAI Exception]: " . $e->getMessage();
        }
    }

    public function generateStructuredOutput(string $prompt, array $jsonSchema = [], array $options = []): array
    {
        $jsonPrompt = $prompt . "\n\nReturn ONLY raw valid JSON format.";
        $rawOutput = $this->generateText($jsonPrompt, $options);
        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawOutput));
        $decoded = json_decode($cleanJson, true);

        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : ['error' => 'Invalid JSON', 'raw' => $rawOutput];
    }

    public function testConnection(): bool
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) return false;

        $endpoint = rtrim(str_replace('/chat/completions', '', $this->provider->api_endpoint ?? 'https://api.openai.com/v1'), '/');
        $response = Http::withToken($apiKey)->timeout(10)->get("{$endpoint}/models");
        return $response->successful();
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-4o-mini' => 'GPT-4o Mini',
            'gpt-4o' => 'GPT-4o',
            'custom-model' => 'Custom Endpoint Model',
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
        return env('OPENAI_API_KEY');
    }
}
