<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAnalysis extends Model
{
    protected $table = 'product_analyses';

    protected $fillable = [
        'product_id',
        'market_demand',
        'target_audience',
        'pain_points',
        'buyer_intent',
        'problem_solved',
        'emotional_triggers',
        'competition_analysis',
        'product_differentiation',
        'pricing_attractiveness',
        'commission_attractiveness',
        'content_potential',
        'viral_potential',
        'seo_opportunity',
        'social_media_fit',
        'risk_factors',
        'compliance_concerns',
        'raw_ai_output',
    ];

    protected $casts = [
        'raw_ai_output' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
