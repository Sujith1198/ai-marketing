<?php

namespace App\DTOs;

use App\Models\Product;

class OpportunityDTO
{
    public function __construct(
        public Product $product,
        public int $overallScore,
        public string $recommendation,
        public int $demandScore,
        public int $buyerIntentScore,
        public int $socialFitScore,
        public bool $isStale,
        public int $completenessScore,
        public array $bestPlatforms = []
    ) {}
}
