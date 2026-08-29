<?php

namespace App\Http\Controllers;

use App\Models\AffiliateNetwork;
use App\Models\Product;
use App\Services\Product\OpportunityRankingService;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    protected OpportunityRankingService $rankingService;

    public function __construct(OpportunityRankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index(Request $request)
    {
        $filters = [
            'network_id' => $request->input('network_id'),
            'category' => $request->input('category'),
            'search' => $request->input('search'),
            'recommendation' => $request->input('recommendation'),
            'min_score' => $request->input('min_score'),
            'sort' => $request->input('sort', 'highest_score'),
        ];

        $opportunities = $this->rankingService->getOpportunities($filters, 12);
        $networks = AffiliateNetwork::where('is_active', true)->get();
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return view('opportunities.index', compact('opportunities', 'networks', 'categories', 'filters'));
    }
}
