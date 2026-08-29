<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\Approval;
use App\Models\Campaign;
use App\Models\Conversion;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Models\ProductScore;
use App\Models\ScheduledPost;
use App\Services\Product\ProductDataCompletenessService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $productsCount = Product::count();
        $productsAnalyzedCount = ProductAnalysis::where('status', 'completed')->distinct('product_id')->count();
        
        $strongPromoteCount = ProductScore::where('recommendation', 'STRONG_PROMOTE')->distinct('product_id')->count();
        $promoteCount = ProductScore::where('recommendation', 'PROMOTE')->distinct('product_id')->count();
        $watchlistCount = Product::where('status', 'watching')->count();

        $activeCampaignsCount = Campaign::where('status', 'active')->count();
        $pendingApprovalsCount = Approval::where('status', 'pending')->count();
        $scheduledPostsCount = ScheduledPost::where('status', 'scheduled')->count();
        $publishedPostsCount = ScheduledPost::where('status', 'published')->count();
        
        $totalClicks = AffiliateClick::count();
        $totalConversions = Conversion::count();
        $totalRevenue = Conversion::sum('commission_amount');

        // Top AI Opportunities
        $opportunities = ProductScore::with(['product.network', 'product.analysis'])
            ->orderBy('overall_opportunity_score', 'desc')
            ->take(5)
            ->get();

        // Recently Analyzed Products
        $recentlyAnalyzed = ProductAnalysis::with(['product.network', 'scores'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Products needing more data
        $completenessService = app(ProductDataCompletenessService::class);
        $productsNeedingData = Product::with('network')
            ->whereNull('description')
            ->orWhereNull('price')
            ->orWhere('commission_value', 0)
            ->take(5)
            ->get();

        // Urgent Pending Approvals
        $pendingApprovals = Approval::with('approvable')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'productsCount',
            'productsAnalyzedCount',
            'strongPromoteCount',
            'promoteCount',
            'watchlistCount',
            'activeCampaignsCount',
            'pendingApprovalsCount',
            'scheduledPostsCount',
            'publishedPostsCount',
            'totalClicks',
            'totalConversions',
            'totalRevenue',
            'opportunities',
            'recentlyAnalyzed',
            'productsNeedingData',
            'pendingApprovals'
        ));
    }
}
