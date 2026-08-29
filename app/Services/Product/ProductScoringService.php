<?php

namespace App\Services\Product;

use App\DTOs\ProductScoreDTO;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Models\SystemSetting;

class ProductScoringService
{
    protected array $weights;
    protected int $maxCompetitionPenalty;
    protected int $maxRiskPenalty;

    public function __construct()
    {
        $this->loadConfiguration();
    }

    public function calculateScore(Product $product, ?ProductAnalysis $analysis = null): ProductScoreDTO
    {
        $aiOutput = $analysis?->raw_ai_output ?? [];

        // Extract or default individual sub-scores (0-100)
        $demand = (int) ($aiOutput['demand_score'] ?? $aiOutput['market_demand']['score'] ?? 70);
        $buyerIntent = (int) ($aiOutput['buyer_intent_score'] ?? $aiOutput['buyer_intent']['score'] ?? 75);
        $contentPotential = (int) ($aiOutput['content_potential_score'] ?? $aiOutput['content_potential']['score'] ?? 70);
        $socialFit = (int) ($aiOutput['social_fit_score'] ?? $this->averageSocialFit($aiOutput) ?? 65);
        $seoPotential = (int) ($aiOutput['seo_potential_score'] ?? $aiOutput['seo_potential']['score'] ?? 65);
        $conversionPotential = (int) ($aiOutput['conversion_potential_score'] ?? 70);
        $trustScore = (int) ($aiOutput['trust_score'] ?? 75);
        $viralPotential = (int) ($aiOutput['viral_potential_score'] ?? 60);

        // Commission Score (Deterministic math based on commission data)
        $commissionScore = $this->calculateCommissionScore($product);

        // Negative Penalties (0-100 input mapped to penalty deductions)
        $competitionScore = (int) ($aiOutput['competition_score'] ?? $aiOutput['competition']['score'] ?? 40);
        $riskScore = (int) ($aiOutput['risk_score'] ?? 20);

        // Calculate Positive Weighted Base Score (0-100)
        $weightedDemand = ($demand * $this->weights['demand']) / 100;
        $weightedIntent = ($buyerIntent * $this->weights['buyer_intent']) / 100;
        $weightedCommission = ($commissionScore * $this->weights['commission']) / 100;
        $weightedContent = ($contentPotential * $this->weights['content_potential']) / 100;
        $weightedSocial = ($socialFit * $this->weights['social_fit']) / 100;
        $weightedSeo = ($seoPotential * $this->weights['seo_potential']) / 100;
        $weightedConversion = ($conversionPotential * $this->weights['conversion_potential']) / 100;
        $weightedTrust = ($trustScore * $this->weights['trust']) / 100;

        $baseScore = $weightedDemand + $weightedIntent + $weightedCommission + $weightedContent +
                     $weightedSocial + $weightedSeo + $weightedConversion + $weightedTrust;

        // Calculate Penalty Deductions
        $competitionPenalty = ($competitionScore / 100) * $this->maxCompetitionPenalty;
        $riskPenalty = ($riskScore / 100) * $this->maxRiskPenalty;

        // Calculate Final Overall Score (Clamped 0-100)
        $rawOverall = $baseScore - $competitionPenalty - $riskPenalty;
        $overallScore = (int) round(min(100, max(0, $rawOverall)));

        // Determine Recommendation
        $recommendation = app(ProductRecommendationService::class)->determineRecommendation($overallScore, $analysis);

        return new ProductScoreDTO(
            demandScore: $demand,
            buyerIntentScore: $buyerIntent,
            competitionScore: $competitionScore,
            commissionScore: $commissionScore,
            contentPotentialScore: $contentPotential,
            viralPotentialScore: $viralPotential,
            seoPotentialScore: $seoPotential,
            trustScore: $trustScore,
            socialFitScore: $socialFit,
            conversionPotentialScore: $conversionPotential,
            riskScore: $riskScore,
            overallScore: $overallScore,
            recommendation: $recommendation,
            breakdown: [
                'base_score' => round($baseScore, 2),
                'competition_penalty' => round($competitionPenalty, 2),
                'risk_penalty' => round($riskPenalty, 2),
                'weights_used' => $this->weights,
            ]
        );
    }

    public function calculateCommissionScore(Product $product): int
    {
        if ($product->commission_type === 'percentage') {
            $val = (float) $product->commission_value;
            if ($val >= 50) return 100;
            if ($val >= 30) return 85;
            if ($val >= 15) return 70;
            if ($val >= 5)  return 50;
            return 30;
        }

        if ($product->commission_type === 'fixed') {
            $val = (float) $product->commission_value;
            if ($val >= 100) return 100;
            if ($val >= 50)  return 85;
            if ($val >= 20)  return 70;
            if ($val >= 5)   return 50;
            return 30;
        }

        return 50; // Neutral fallback for missing commission info
    }

    protected function averageSocialFit(array $aiOutput): int
    {
        $fits = $aiOutput['social_fit'] ?? [];
        if (is_array($fits) && !empty($fits)) {
            $nums = array_filter($fits, 'is_numeric');
            if (!empty($nums)) {
                return (int) round(array_sum($nums) / count($nums));
            }
        }
        return 65;
    }

    protected function loadConfiguration(): void
    {
        try {
            $savedWeights = SystemSetting::getSetting('scoring_weights');
            $this->maxCompetitionPenalty = (int) SystemSetting::getSetting('max_competition_penalty', 15);
            $this->maxRiskPenalty = (int) SystemSetting::getSetting('max_risk_penalty', 20);
        } catch (\Exception $e) {
            $savedWeights = null;
            $this->maxCompetitionPenalty = 15;
            $this->maxRiskPenalty = 20;
        }

        $this->weights = $savedWeights ?? [
            'demand' => 15,
            'buyer_intent' => 15,
            'commission' => 10,
            'content_potential' => 15,
            'social_fit' => 10,
            'seo_potential' => 10,
            'conversion_potential' => 15,
            'trust' => 10,
        ];
    }
}
