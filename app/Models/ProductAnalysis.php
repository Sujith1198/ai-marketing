<?php

namespace App\Models;

use App\Enums\AnalysisStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAnalysis extends Model
{
    protected $table = 'product_analyses';

    protected $fillable = [
        'product_id',
        'analysis_version',
        'provider',
        'model',
        'status',
        'confidence_score',
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
        'status' => AnalysisStatus::class,
        'confidence_score' => 'integer',
        'target_audience' => 'array',
        'pain_points' => 'array',
        'emotional_triggers' => 'array',
        'social_media_fit' => 'array',
        'risk_factors' => 'array',
        'compliance_concerns' => 'array',
        'raw_ai_output' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ProductScore::class, 'product_analysis_id');
    }
}
