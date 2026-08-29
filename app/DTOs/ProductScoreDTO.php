<?php

namespace App\DTOs;

class ProductScoreDTO
{
    public function __construct(
        public int $demandScore,
        public int $buyerIntentScore,
        public int $competitionScore,
        public int $commissionScore,
        public int $contentPotentialScore,
        public int $viralPotentialScore,
        public int $seoPotentialScore,
        public int $trustScore,
        public int $socialFitScore,
        public int $conversionPotentialScore,
        public int $riskScore,
        public int $overallScore,
        public string $recommendation,
        public array $breakdown = []
    ) {}
}
