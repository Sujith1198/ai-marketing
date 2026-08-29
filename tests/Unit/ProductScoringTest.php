<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Services\Product\ProductRecommendationService;
use App\Services\Product\ProductScoringService;
use Tests\TestCase;

class ProductScoringTest extends TestCase
{
    public function test_product_scoring_math_and_clamping()
    {
        $scoringService = new ProductScoringService();
        $product = new Product([
            'commission_type' => 'percentage',
            'commission_value' => 50,
        ]);

        $scoreDTO = $scoringService->calculateScore($product);

        $this->assertGreaterThanOrEqual(0, $scoreDTO->overallScore);
        $this->assertLessThanOrEqual(100, $scoreDTO->overallScore);
        $this->assertEquals(100, $scoreDTO->commissionScore);
    }

    public function test_recommendation_thresholds_and_compliance_blocks()
    {
        $recommendationService = new ProductRecommendationService();

        $recStrong = $recommendationService->determineRecommendation(90);
        $this->assertEquals('STRONG_PROMOTE', $recStrong);

        $recAvoid = $recommendationService->determineRecommendation(20);
        $this->assertEquals('AVOID', $recAvoid);

        // Compliance Block test
        $blockedAnalysis = new ProductAnalysis([
            'compliance_concerns' => ['Medical claim risk - policy violation'],
        ]);

        $recBlocked = $recommendationService->determineRecommendation(95, $blockedAnalysis);
        $this->assertEquals('WATCH', $recBlocked); // Overridden due to compliance block
    }
}
