<?php

namespace App\Http\Controllers;

use App\Models\AffiliateNetwork;
use App\Models\ProductScore;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductScore::with(['product.network', 'product.analysis']);

        if ($request->filled('recommendation')) {
            $query->where('recommendation', $request->input('recommendation'));
        }
        if ($request->filled('min_score')) {
            $query->where('overall_opportunity_score', '>=', (int) $request->input('min_score'));
        }

        $scores = $query->orderBy('overall_opportunity_score', 'desc')->paginate(12);
        $networks = AffiliateNetwork::where('is_active', true)->get();

        return view('opportunities.index', compact('scores', 'networks'));
    }
}
