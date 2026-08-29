<?php

namespace App\Services\Scoring;

use App\Enums\RecommendationLevel;
use App\Models\Product;
use App\Models\ProductScore;

class WeightedScoringEngine
{
    /**
     * Configurable scoring weights.
     */
    protected array $weights = [
        'demand' => 0.15,
        'buyer_intent' => 0.15,
        'content_potential' => 0.15,
        'conversion_potential' => 0.15,
        'trust' => 0.10,
        'social_fit' => 0.10,
        'seo_potential' => 0.10,
        'commission' => 0.10,
    ];

    /**
     * Calculate product opportunity score and recommendation.
     */
    public function calculateScore(Product $product, array $rawScores): ProductScore
    {
        $demand = min(100, max(0, $rawScores['demand_score'] ?? 70));
        $buyerIntent = min(100, max(0, $rawScores['buyer_intent_score'] ?? 75));
        $competition = min(100, max(0, $rawScores['competition_score'] ?? 50));
        $commission = min(100, max(0, $rawScores['commission_score'] ?? 80));
        $contentPotential = min(100, max(0, $rawScores['content_potential_score'] ?? 80));
        $viralPotential = min(100, max(0, $rawScores['viral_potential_score'] ?? 65));
        $seoPotential = min(100, max(0, $rawScores['seo_potential_score'] ?? 70));
        $trust = min(100, max(0, $rawScores['trust_score'] ?? 85));
        $socialFit = min(100, max(0, $rawScores['social_fit_score'] ?? 80));
        $conversionPotential = min(100, max(0, $rawScores['conversion_potential_score'] ?? 75));
        $riskScore = min(100, max(0, $rawScores['risk_score'] ?? 15));

        // Base weighted score
        $weightedBase = ($demand * $this->weights['demand'])
            + ($buyerIntent * $this->weights['buyer_intent'])
            + ($contentPotential * $this->weights['content_potential'])
            + ($conversionPotential * $this->weights['conversion_potential'])
            + ($trust * $this->weights['trust'])
            + ($socialFit * $this->weights['social_fit'])
            + ($seoPotential * $this->weights['seo_potential'])
            + ($commission * $this->weights['commission']);

        // Apply Penalties
        $penalties = 0;
        if ($riskScore > 50) {
            $penalties += ($riskScore - 50) * 0.5; // High risk penalty
        }
        if ($competition > 80) {
            $penalties += 10; // Extremely fierce competition penalty
        }
        if ($commission < 20) {
            $penalties += 15; // Low commission penalty
        }

        $overallScore = (int) round(max(0, min(100, $weightedBase - $penalties)));

        // Determine recommendation enum
        $recommendation = match(true) {
            $overallScore >= 85 => RecommendationLevel::STRONG_PROMOTE->value,
            $overallScore >= 70 => RecommendationLevel::PROMOTE->value,
            $overallScore >= 55 => RecommendationLevel::TEST->value,
            $overallScore >= 40 => RecommendationLevel::WATCH->value,
            default => RecommendationLevel::AVOID->value,
        };

        return ProductScore::updateOrCreate(
            ['product_id' => $product->id],
            [
                'demand_score' => $demand,
                'buyer_intent_score' => $buyerIntent,
                'competition_score' => $competition,
                'commission_score' => $commission,
                'content_potential_score' => $contentPotential,
                'viral_potential_score' => $viralPotential,
                'seo_potential_score' => $seoPotential,
                'trust_score' => $trust,
                'social_fit_score' => $socialFit,
                'conversion_potential_score' => $conversionPotential,
                'risk_score' => $riskScore,
                'overall_opportunity_score' => $overallScore,
                'recommendation' => $recommendation,
                'score_breakdown' => [
                    'weighted_base' => round($weightedBase, 2),
                    'penalties' => round($penalties, 2),
                    'weights_used' => $this->weights,
                ],
            ]
        );
    }
}
