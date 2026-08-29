<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\Campaign;
use App\Models\Conversion;
use App\Models\Product;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $clicksCount = AffiliateClick::count();
        $conversionsCount = Conversion::count();
        $totalRevenue = Conversion::sum('commission_amount');
        $ctr = $clicksCount > 0 ? round(($conversionsCount / $clicksCount) * 100, 2) : 0.00;

        $topCampaigns = Campaign::withCount('contents')->take(5)->get();
        $topProducts = Product::with('score')->take(5)->get();

        return view('analytics.index', compact('clicksCount', 'conversionsCount', 'totalRevenue', 'ctr', 'topCampaigns', 'topProducts'));
    }
}
