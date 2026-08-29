<?php

namespace App\Services\AI\Providers;

use App\Models\AIProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\Security\SecureVaultService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
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
        $model = $options['model'] ?? $this->provider->default_model ?? 'gemini-1.5-flash';

        if (empty($apiKey)) {
            return "[Gemini Provider Notice]: API key is missing or not configured. (Manual Mode Active)";
        }

        try {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            
            $response = Http::timeout(30)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'maxOutputTokens' => $options['max_tokens'] ?? 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            }

            Log::error("Gemini API Error", ['status' => $response->status(), 'body' => $response->body()]);
            return "[Gemini Error]: Request failed with HTTP status " . $response->status();

        } catch (\Exception $e) {
            Log::error("Gemini Exception: " . $e->getMessage());
            return "[Gemini Exception]: " . $e->getMessage();
        }
    }

    public function generateStructuredOutput(string $prompt, array $jsonSchema = [], array $options = []): array
    {
        $jsonPrompt = $prompt . "\n\nCRITICAL REQUIREMENT: Return ONLY a valid JSON object matching the expected schema. Do not include markdown code blocks or additional text.";
        $rawOutput = $this->generateText($jsonPrompt, $options);
        
        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawOutput));
        $decoded = json_decode($cleanJson, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Retry once with repair prompt
        $repairPrompt = "Repair the following text to ensure it is valid JSON:\n\n" . $rawOutput;
        $repairedText = $this->generateText($repairPrompt, $options);
        $repairedJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($repairedText));
        $repairedDecoded = json_decode($repairedJson, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($repairedDecoded)) {
            return $repairedDecoded;
        }

        return ['error' => 'Failed to parse JSON output', 'raw' => $rawOutput];
    }

    public function testConnection(): bool
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) return false;

        $response = Http::timeout(10)->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
        return $response->successful();
    }

    public function getAvailableModels(): array
    {
        return [
            'gemini-1.5-flash' => 'Gemini 1.5 Flash (Fast & Cost Effective)',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro (High Reasoning)',
            'gemini-2.0-flash' => 'Gemini 2.0 Flash (Next-Gen Speed)',
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
        return env('GEMINI_API_KEY');
    }
}
