<?php

namespace App\Services\Product;

use App\Enums\RecommendationLevel;
use App\Models\ProductAnalysis;

class ProductRecommendationService
{
    public function determineRecommendation(int $overallScore, ?ProductAnalysis $analysis = null): string
    {
        // Default threshold evaluation
        $recommendation = match (true) {
            $overallScore >= 85 => RecommendationLevel::STRONG_PROMOTE->value,
            $overallScore >= 70 => RecommendationLevel::PROMOTE->value,
            $overallScore >= 55 => RecommendationLevel::TEST->value,
            $overallScore >= 40 => RecommendationLevel::WATCH->value,
            default => RecommendationLevel::AVOID->value,
        };

        // Check for compliance flags / risk blocks
        if ($analysis && $this->hasComplianceBlock($analysis)) {
            if ($recommendation === RecommendationLevel::STRONG_PROMOTE->value || $recommendation === RecommendationLevel::PROMOTE->value) {
                return RecommendationLevel::WATCH->value; // Downgrade due to compliance concerns
            }
        }

        return $recommendation;
    }

    public function hasComplianceBlock(ProductAnalysis $analysis): bool
    {
        $concerns = $analysis->compliance_concerns ?? [];
        $risks = $analysis->risk_factors ?? [];

        $criticalKeywords = ['blocked', 'medical claim', 'financial claim', 'misleading', 'restricted', 'policy violation'];

        foreach (array_merge((array)$concerns, (array)$risks) as $item) {
            $text = is_string($item) ? strtolower($item) : json_encode($item);
            foreach ($criticalKeywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }
}
