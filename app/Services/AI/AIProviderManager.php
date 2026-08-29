<?php

namespace App\Services\AI;

use App\Models\AIProvider;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\CustomOpenAIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\GroqProvider;
use App\Services\AI\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Log;

class AIProviderManager
{
    /**
     * Resolve provider interface instance.
     */
    public function resolve(?AIProvider $provider = null): AIProviderInterface
    {
        if (!$provider) {
            $provider = AIProvider::where('is_primary', true)->where('is_active', true)->first()
                ?? AIProvider::where('is_active', true)->first();
        }

        if (!$provider) {
            // Return a default stub provider if no providers configured
            return new GeminiProvider(new AIProvider([
                'name' => 'Default Gemini',
                'driver' => 'gemini',
                'default_model' => 'gemini-1.5-flash',
            ]));
        }

        return match($provider->driver) {
            'gemini' => new GeminiProvider($provider),
            'groq' => new GroqProvider($provider),
            'openrouter' => new OpenRouterProvider($provider),
            'custom_openai', 'openai' => new CustomOpenAIProvider($provider),
            default => new GeminiProvider($provider),
        };
    }

    /**
     * Generate text with fallback chain support.
     */
    public function generateTextWithFallback(string $prompt, array $options = []): string
    {
        $primaryProviderModel = AIProvider::where('is_primary', true)->where('is_active', true)->first();
        $provider = $this->resolve($primaryProviderModel);

        $result = $provider->generateText($prompt, $options);

        if ($this->isErrorResult($result) && $primaryProviderModel && $primaryProviderModel->fallback_provider_id) {
            Log::warning("Primary AI Provider failed. Triggering Fallback Provider...", ['primary' => $primaryProviderModel->name]);
            $fallbackModel = AIProvider::find($primaryProviderModel->fallback_provider_id);
            if ($fallbackModel && $fallbackModel->is_active) {
                $fallbackProvider = $this->resolve($fallbackModel);
                return $fallbackProvider->generateText($prompt, $options);
            }
        }

        return $result;
    }

    protected function isErrorResult(string $result): bool
    {
        return str_contains($result, '[Gemini Error]') ||
               str_contains($result, '[Groq Error]') ||
               str_contains($result, '[OpenRouter Error]') ||
               str_contains($result, '[Custom OpenAI Error]');
    }
}
