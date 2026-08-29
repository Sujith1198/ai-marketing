@extends('layouts.app')

@section('title', 'CEO Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1">CEO Marketing Operating System</h2>
        <p class="text-muted mb-0">Overview of AI team recommendations, opportunity scores, and campaign performance.</p>
    </div>
    <a href="{{ route('ai-team.chat') }}" class="btn btn-primary">
        <i class="bi bi-chat-dots-fill me-1"></i> Start AI Team Meeting
    </a>
</div>

<!-- Key SaaS & Phase 2 Performance Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Products In Catalog</span>
                <h3 class="fw-bold mb-0 mt-1">{{ $productsCount }}</h3>
                <small class="text-muted">{{ $productsAnalyzedCount }} Analyzed by AI</small>
            </div>
            <div class="metric-icon bg-primary-subtle text-primary">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Strong Promote Opportunities</span>
                <h3 class="fw-bold mb-0 mt-1 text-success">{{ $strongPromoteCount }}</h3>
                <small class="text-muted">{{ $promoteCount }} Promote Tier</small>
            </div>
            <div class="metric-icon bg-success-subtle text-success">
                <i class="bi bi-star-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Product Watchlist</span>
                <h3 class="fw-bold mb-0 mt-1 text-warning">{{ $watchlistCount }}</h3>
                <small class="text-muted">Saved for re-analysis</small>
            </div>
            <div class="metric-icon bg-warning-subtle text-warning">
                <i class="bi bi-eye"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Active Campaigns</span>
                <h3 class="fw-bold mb-0 mt-1 text-info">{{ $activeCampaignsCount }}</h3>
                <small class="text-muted">${{ number_format($totalRevenue, 2) }} Revenue</small>
            </div>
            <div class="metric-icon bg-info-subtle text-info">
                <i class="bi bi-megaphone"></i>
            </div>
        </div>
    </div>
</div>

<!-- Today's AI Opportunities Section -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-stars text-primary me-2"></i>Top AI Opportunities</h4>
            <p class="text-muted small mb-0">Highest scoring affiliate products discovered and evaluated by AI Agents.</p>
        </div>
        <a href="{{ route('opportunities.index') }}" class="btn btn-outline-primary btn-sm">View All Opportunities</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product Name</th>
                    <th>Network</th>
                    <th>Demand</th>
                    <th>Intent</th>
                    <th>Commission</th>
                    <th>Overall Score</th>
                    <th>Recommendation</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opportunities as $score)
                    <tr>
                        <td>
                            <a href="{{ route('products.show', $score->product->id) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $score->product->name }}
                            </a>
                            <span class="text-muted extra-small d-block">{{ $score->product->category }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ optional($score->product->network)->name }}</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->demand_score }}/100</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->buyer_intent_score }}/100</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->commission_score }}/100</span></td>
                        <td>
                            <span class="badge bg-primary fs-6">{{ $score->overall_opportunity_score }}/100</span>
                        </td>
                        <td>
                            <span class="badge {{ $score->badgeClass() }}">{{ $score->recommendationLabel() }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('campaigns.wizard', ['product_id' => $score->product->id]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-rocket-takeoff me-1"></i> Create Campaign
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No AI opportunities analyzed yet. Add products to analyze!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Products Needing More Data Warning Widget -->
@if($productsNeedingData->count() > 0)
    <div class="card-custom p-4 mb-4 border-start border-4 border-warning">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-bold text-warning mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Products Needing More Information</h5>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-warning">Update Products</a>
        </div>
        <p class="small text-muted mb-3">These products have incomplete pricing or description data which reduces AI analysis confidence:</p>

        <div class="row g-2">
            @foreach($productsNeedingData as $pData)
                <div class="col-md-4">
                    <div class="bg-light p-2 rounded border small">
                        <strong>{{ $pData->name }}</strong>
                        <span class="text-muted d-block">Missing Price / Description</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
