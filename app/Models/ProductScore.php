<?php

namespace App\Models;

use App\Enums\RecommendationLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductScore extends Model
{
    protected $table = 'product_scores';

    protected $fillable = [
        'product_id',
        'demand_score',
        'buyer_intent_score',
        'competition_score',
        'commission_score',
        'content_potential_score',
        'viral_potential_score',
        'seo_potential_score',
        'trust_score',
        'social_fit_score',
        'conversion_potential_score',
        'risk_score',
        'overall_opportunity_score',
        'recommendation',
        'score_breakdown',
    ];

    protected $casts = [
        'demand_score' => 'integer',
        'buyer_intent_score' => 'integer',
        'competition_score' => 'integer',
        'commission_score' => 'integer',
        'content_potential_score' => 'integer',
        'viral_potential_score' => 'integer',
        'seo_potential_score' => 'integer',
        'trust_score' => 'integer',
        'social_fit_score' => 'integer',
        'conversion_potential_score' => 'integer',
        'risk_score' => 'integer',
        'overall_opportunity_score' => 'integer',
        'score_breakdown' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function recommendationLabel(): string
    {
        $recEnum = RecommendationLevel::tryFrom($this->recommendation);
        return $recEnum ? $recEnum->label() : $this->recommendation;
    }

    public function badgeClass(): string
    {
        $recEnum = RecommendationLevel::tryFrom($this->recommendation);
        return $recEnum ? $recEnum->badgeClass() : 'bg-secondary';
    }
}
