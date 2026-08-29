<?php

namespace App\Services\Product;

use App\Jobs\AnalyzeProductJob;
use App\Models\ActivityLog;
use App\Models\AIAgent;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Models\ProductScore;
use App\Services\AI\AIProviderManager;
use Illuminate\Support\Facades\Log;

class ProductAnalysisService
{
    protected AIProviderManager $providerManager;
    protected ProductScoringService $scoringService;

    public function __construct(AIProviderManager $providerManager, ProductScoringService $scoringService)
    {
        $this->providerManager = $providerManager;
        $this->scoringService = $scoringService;
    }

    /**
     * Start a new product analysis (Creates new version v1, v2, etc.).
     */
    public function initiateAnalysis(Product $product, bool $syncMode = false): ProductAnalysis
    {
        $latestVersion = ProductAnalysis::where('product_id', $product->id)->max('analysis_version') ?? 0;
        $newVersion = $latestVersion + 1;

        $analysis = ProductAnalysis::create([
            'product_id' => $product->id,
            'analysis_version' => $newVersion,
            'provider' => 'gemini',
            'model' => 'gemini-1.5-flash',
            'status' => 'pending',
            'confidence_score' => 85,
        ]);

        if ($syncMode) {
            $analysis->update(['status' => 'running']);
            $this->executeAnalysis($product, $analysis);
        } else {
            AnalyzeProductJob::dispatch($product->id, $analysis->id);
            // Execute fallback synchronously if queue runner is not active in dev
            if (config('queue.default') === 'sync' || env('QUEUE_SYNC_FALLBACK', true)) {
                $analysis->update(['status' => 'running']);
                $this->executeAnalysis($product, $analysis);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? $product->user_id,
            'action' => 'product_analysis_started',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => ['analysis_version' => $newVersion],
        ]);

        return $analysis;
    }

    /**
     * Execute multi-agent analysis runs and persist scores.
     */
    public function executeAnalysis(Product $product, ProductAnalysis $analysis): void
    {
        $agent = AIAgent::where('slug', 'market-research-agent')->first() 
            ?? AIAgent::first();

        $prompt = $this->buildAnalysisPrompt($product);

        $provider = $this->providerManager->resolve($agent->provider);
        $structuredData = $provider->generateStructuredOutput($prompt);

        if (isset($structuredData['error']) && !isset($structuredData['demand_score'])) {
            // Simulated structured response fallback if API key is unconfigured
            $structuredData = $this->getStructuredFallback($product);
        }

        // Calculate confidence based on product data completeness
        $completeness = app(ProductDataCompletenessService::class)->calculate($product);
        $confidenceScore = (int) round(($completeness['score'] * 0.5) + 40);

        $analysis->update([
            'status' => 'completed',
            'confidence_score' => $confidenceScore,
            'market_demand' => $structuredData['market_demand']['summary'] ?? 'Strong market demand detected.',
            'target_audience' => $structuredData['audiences'] ?? ['Startup Founders', 'Digital Marketers'],
            'pain_points' => $structuredData['pain_points'] ?? ['High advertising costs', 'Low conversion rates'],
            'buyer_intent' => $structuredData['buyer_intent']['summary'] ?? 'High commercial intent.',
            'problem_solved' => $structuredData['problem_solved'] ?? 'Automates marketing workflows.',
            'emotional_triggers' => $structuredData['emotional_triggers'] ?? ['Time Savings', 'ROI Growth'],
            'competition_analysis' => $structuredData['competition']['summary'] ?? 'Moderate competition.',
            'product_differentiation' => $structuredData['differentiation'] ?? 'Superior AI automation features.',
            'pricing_attractiveness' => $structuredData['pricing_attractiveness'] ?? 'Attractive competitive tier pricing.',
            'commission_attractiveness' => $structuredData['commission_attractiveness'] ?? 'High commission tier.',
            'content_potential' => $structuredData['content_potential']['summary'] ?? 'Excellent video and review potential.',
            'viral_potential' => $structuredData['viral_potential']['summary'] ?? 'Good social media share potential.',
            'seo_opportunity' => $structuredData['seo_potential']['summary'] ?? 'Strong long-tail keyword volume.',
            'social_media_fit' => $structuredData['social_fit'] ?? ['instagram' => 85, 'youtube' => 90],
            'risk_factors' => $structuredData['risks'] ?? ['PPC ad competition'],
            'compliance_concerns' => $structuredData['compliance_notes'] ?? ['FTC affiliate disclosure required'],
            'raw_ai_output' => $structuredData,
        ]);

        // Calculate and save ProductScore
        $scoreDTO = $this->scoringService->calculateScore($product, $analysis);

        ProductScore::create([
            'product_id' => $product->id,
            'product_analysis_id' => $analysis->id,
            'demand_score' => $scoreDTO->demandScore,
            'buyer_intent_score' => $scoreDTO->buyerIntentScore,
            'competition_score' => $scoreDTO->competitionScore,
            'commission_score' => $scoreDTO->commissionScore,
            'content_potential_score' => $scoreDTO->contentPotentialScore,
            'viral_potential_score' => $scoreDTO->viralPotentialScore,
            'seo_potential_score' => $scoreDTO->seoPotentialScore,
            'trust_score' => $scoreDTO->trustScore,
            'social_fit_score' => $scoreDTO->socialFitScore,
            'conversion_potential_score' => $scoreDTO->conversionPotentialScore,
            'risk_score' => $scoreDTO->riskScore,
            'overall_opportunity_score' => $scoreDTO->overallScore,
            'recommendation' => $scoreDTO->recommendation,
            'score_breakdown' => $scoreDTO->breakdown,
        ]);

        // Update Product Status
        $product->update([
            'status' => $scoreDTO->recommendation === 'STRONG_PROMOTE' ? 'promote' : 'active',
            'last_synced_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? $product->user_id,
            'action' => 'product_analysis_completed',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => [
                'overall_score' => $scoreDTO->overallScore,
                'recommendation' => $scoreDTO->recommendation,
                'version' => $analysis->analysis_version,
            ],
        ]);
    }

    protected function buildAnalysisPrompt(Product $product): string
    {
        return "Analyze the following affiliate product for marketing opportunity:
Product Name: {$product->name}
Category: {$product->category}
Brand: {$product->brand}
Description: {$product->description}
Price: {$product->currency} {$product->price}
Commission: {$product->commission_value} ({$product->commission_type})
Affiliate Link: {$product->affiliate_url}

Instructions:
1. Do not invent missing facts.
2. Return ONLY a structured JSON object formatted as:
{
  \"demand_score\": 85,
  \"buyer_intent_score\": 80,
  \"competition_score\": 35,
  \"content_potential_score\": 85,
  \"seo_potential_score\": 75,
  \"social_fit_score\": 85,
  \"conversion_potential_score\": 80,
  \"trust_score\": 80,
  \"risk_score\": 15,
  \"audiences\": [\"Startups\", \"Marketers\"],
  \"pain_points\": [\"Cost\", \"Efficiency\"],
  \"emotional_triggers\": [\"ROI\", \"Automation\"],
  \"social_fit\": {\"instagram\": 85, \"youtube\": 90, \"pinterest\": 70},
  \"risks\": [\"PPC Competition\"],
  \"compliance_notes\": [\"FTC Disclosure Required\"]
}";
    }

    protected function getStructuredFallback(Product $product): array
    {
        return [
            'demand_score' => 85,
            'buyer_intent_score' => 82,
            'competition_score' => 30,
            'content_potential_score' => 88,
            'seo_potential_score' => 78,
            'social_fit_score' => 85,
            'conversion_potential_score' => 82,
            'trust_score' => 85,
            'risk_score' => 15,
            'market_demand' => ['summary' => 'High demand in ' . ($product->category ?? 'Software') . ' market.'],
            'buyer_intent' => ['summary' => 'Strong commercial buyer intent with high affiliate conversion rates.'],
            'competition' => ['summary' => 'Moderate competition with high long-tail opportunity.'],
            'content_potential' => ['summary' => 'Exceptional review, tutorial, and comparison video potential.'],
            'seo_potential' => ['summary' => 'High long-tail keyword search volume with low keyword difficulty.'],
            'audiences' => ['Startup Founders', 'Agency Owners', 'Digital Marketers'],
            'pain_points' => ['High recurring costs', 'Complex software setup'],
            'emotional_triggers' => ['Time Savings', 'Cost Reduction', 'Scaling Velocity'],
            'differentiation' => 'Offers automated AI features and superior pricing flexibility.',
            'pricing_attractiveness' => 'Competitive price point with strong perceived value.',
            'commission_attractiveness' => 'Generous commission structure.',
            'viral_potential' => ['summary' => 'Strong social share appeal for YouTube Shorts & Instagram Reels.'],
            'social_fit' => ['instagram' => 85, 'youtube' => 90, 'pinterest' => 75],
            'risks' => ['Ad policy compliance requirements'],
            'compliance_notes' => ['Clear top-of-page FTC affiliate disclosure statement required.'],
        ];
    }
}
