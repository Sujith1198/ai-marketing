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
            return $this->generateSimulatedResponse($prompt);
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

    protected function generateSimulatedResponse(string $prompt): string
    {
        $roleSection = str_contains($prompt, 'System Role:') ? explode("\n\nUser Question:", $prompt)[0] : $prompt;

        if (str_contains($roleSection, 'Product Hunter')) {
            return "### Product Analysis & Discovery\n- **Target Category**: High-Ticket AI Hosting & Enterprise SaaS\n- **Recommended Network**: Hostinger Affiliate & Custom Cloud Partners\n- **Commission Target**: 70% Recurring Monthly Payout\n- **Key Value Prop**: Unlimited NVMe bandwidth, automated AI site builder, and 24/7 priority support for tech startups.";
        }
        if (str_contains($roleSection, 'Market Research')) {
            return "### Target Audience & Pain Points\n- **Demographics**: Startup Founders, CTOs, Agency Owners (Ages 25-45)\n- **Pain Points**: High AWS/GCP cloud costs, complex server maintenance, lack of automated scaling.\n- **Buyer Intent**: High commercial intent; searching for 'Cost-effective scalable AI hosting for startups'.";
        }
        if (str_contains($roleSection, 'Copywriter')) {
            return "### Direct Response Ad Hooks & Headlines\n- **Hook 1**: 'Stop Paying $500/mo for AWS — Host your AI App for 70% Less with Guaranteed 99.9% Uptime.'\n- **Email Subject**: 'How 450+ Tech Startups Scaled Their Cloud Infra in 2026'\n- **Call-To-Action**: 'Claim 70% Exclusive Founder Discount Today ->'";
        }
        if (str_contains($roleSection, 'SEO')) {
            return "### Search Keyword Opportunities\n- **Primary Keywords**: `best hostinger affiliate hosting`, `cheap ai server hosting for startups`\n- **Long-Tail Focus**: `how to host python ai backend for cheap` (KD: 18, Search Vol: 4,200/mo)\n- **Content Format**: In-depth comparison review & benchmark speed test.";
        }
        if (str_contains($roleSection, 'Compliance')) {
            return "### Compliance & Disclosure Audit\n- **FTC Disclosure Standard**: Requires clear top-of-page disclosure: *'Affiliate Disclosure: We may earn a commission if you purchase through our links.'*\n- **Trademark Rules**: Do not bid on branded PPC search keywords.\n- **Claims Audit**: Ensure '70% recurring commission' is explicitly verified in affiliate terms.";
        }
        if (str_contains($roleSection, 'Social Media')) {
            return "### Multi-Platform Content Distribution Plan\n- **Instagram Reels / YouTube Shorts**: 30-sec speed test comparison video.\n- **LinkedIn Carousel**: '5 Cloud Hosting Mistakes Costing Startups $10k/Year'.\n- **Pinterest Pin**: Infographic on '2026 SaaS Infrastructure Cost Benchmark'.";
        }

        return "### Executive Strategy Synthesis\n- **Campaign Status**: High Profit Potential\n- **Recommended Budget**: $250 Initial Test Campaign\n- **Target ROI**: 340% Projected Return on Ad Spend.";
    }
}
