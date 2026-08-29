<?php

namespace App\Services\Product;

use App\DTOs\OpportunityDTO;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OpportunityRankingService
{
    protected ProductDataCompletenessService $completenessService;
    protected const STALE_DAYS = 30;

    public function __construct(ProductDataCompletenessService $completenessService)
    {
        $this->completenessService = $completenessService;
    }

    public function getOpportunities(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with(['network', 'account', 'analysis', 'score'])
            ->whereIn('status', ['active', 'promote', 'watching', 'draft']);

        // Network filter
        if (!empty($filters['network_id'])) {
            $query->where('affiliate_network_id', $filters['network_id']);
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Search query
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Recommendation filter
        if (!empty($filters['recommendation'])) {
            $rec = $filters['recommendation'];
            $query->whereHas('score', function ($q) use ($rec) {
                $q->where('recommendation', $rec);
            });
        }

        // Minimum score filter
        if (!empty($filters['min_score'])) {
            $minScore = (int) $filters['min_score'];
            $query->whereHas('score', function ($q) use ($minScore) {
                $q->where('overall_opportunity_score', '>=', $minScore);
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'highest_score';
        switch ($sort) {
            case 'highest_buyer_intent':
                $query->join('product_scores', 'products.id', '=', 'product_scores.product_id')
                      ->orderBy('product_scores.buyer_intent_score', 'desc');
                break;
            case 'highest_demand':
                $query->join('product_scores', 'products.id', '=', 'product_scores.product_id')
                      ->orderBy('product_scores.demand_score', 'desc');
                break;
            case 'highest_social_fit':
                $query->join('product_scores', 'products.id', '=', 'product_scores.product_id')
                      ->orderBy('product_scores.social_fit_score', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'highest_score':
            default:
                $query->leftJoin('product_scores', 'products.id', '=', 'product_scores.product_id')
                      ->orderBy('product_scores.overall_opportunity_score', 'desc');
                break;
        }

        $paginator = $query->select('products.*')->paginate($perPage);

        // Map items to OpportunityDTO
        $paginator->getCollection()->transform(function (Product $product) {
            $score = $product->score;
            $analysis = $product->analysis;

            $overallScore = $score ? $score->overall_opportunity_score : 0;
            $recommendation = $score ? $score->recommendation : 'TEST';
            $demand = $score ? $score->demand_score : 0;
            $intent = $score ? $score->buyer_intent_score : 0;
            $social = $score ? $score->social_fit_score : 0;

            $isStale = false;
            if ($analysis) {
                $isStale = $analysis->created_at->diffInDays(now()) > self::STALE_DAYS;
            }

            $completeness = $this->completenessService->calculate($product);

            $bestPlatforms = $analysis ? ($analysis->recommended_platforms ?? ['Instagram', 'YouTube']) : ['Instagram'];

            return new OpportunityDTO(
                product: $product,
                overallScore: $overallScore,
                recommendation: $recommendation,
                demandScore: $demand,
                buyerIntentScore: $intent,
                socialFitScore: $social,
                isStale: $isStale,
                completenessScore: $completeness['score'],
                bestPlatforms: (array)$bestPlatforms
            );
        });

        return $paginator;
    }
}
