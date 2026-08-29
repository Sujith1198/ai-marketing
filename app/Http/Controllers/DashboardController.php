<?php

namespace App\Http\Controllers;

use App\Models\AffiliateClick;
use App\Models\Approval;
use App\Models\Campaign;
use App\Models\Conversion;
use App\Models\Product;
use App\Models\ProductScore;
use App\Models\ScheduledPost;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $productsCount = Product::count();
        $activeCampaignsCount = Campaign::where('status', 'active')->count();
        $pendingApprovalsCount = Approval::where('status', 'pending')->count();
        $scheduledPostsCount = ScheduledPost::where('status', 'scheduled')->count();
        $publishedPostsCount = ScheduledPost::where('status', 'published')->count();
        
        $totalClicks = AffiliateClick::count();
        $totalConversions = Conversion::count();
        $totalRevenue = Conversion::sum('commission_amount');

        // Today's AI Opportunities (top 5 product scores >= 70)
        $opportunities = ProductScore::with(['product.network', 'product.analysis'])
            ->orderBy('overall_opportunity_score', 'desc')
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
            'activeCampaignsCount',
            'pendingApprovalsCount',
            'scheduledPostsCount',
            'publishedPostsCount',
            'totalClicks',
            'totalConversions',
            'totalRevenue',
            'opportunities',
            'pendingApprovals'
        ));
    }
}
