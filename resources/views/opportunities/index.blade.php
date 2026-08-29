@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>Opportunity Center</h2>
            <p class="text-muted mb-0">Discover top-ranked affiliate opportunities scored by AI market demand and conversion potential.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card-custom p-3 mb-4">
        <form method="GET" action="{{ route('opportunities.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search product name..." value="{{ $filters['search'] ?? '' }}">
            </div>

            <div class="col-md-2">
                <select name="network_id" class="form-select">
                    <option value="">All Networks</option>
                    @foreach($networks as $net)
                        <option value="{{ $net->id }}" {{ ($filters['network_id'] ?? '') == $net->id ? 'selected' : '' }}>{{ $net->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="recommendation" class="form-select">
                    <option value="">All Recommendations</option>
                    <option value="STRONG_PROMOTE" {{ ($filters['recommendation'] ?? '') == 'STRONG_PROMOTE' ? 'selected' : '' }}>Strong Promote</option>
                    <option value="PROMOTE" {{ ($filters['recommendation'] ?? '') == 'PROMOTE' ? 'selected' : '' }}>Promote</option>
                    <option value="TEST" {{ ($filters['recommendation'] ?? '') == 'TEST' ? 'selected' : '' }}>Test</option>
                    <option value="WATCH" {{ ($filters['recommendation'] ?? '') == 'WATCH' ? 'selected' : '' }}>Watch</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="highest_score" {{ ($filters['sort'] ?? '') == 'highest_score' ? 'selected' : '' }}>Sort: Highest Overall Score</option>
                    <option value="highest_buyer_intent" {{ ($filters['sort'] ?? '') == 'highest_buyer_intent' ? 'selected' : '' }}>Sort: Highest Buyer Intent</option>
                    <option value="highest_demand" {{ ($filters['sort'] ?? '') == 'highest_demand' ? 'selected' : '' }}>Sort: Highest Demand</option>
                    <option value="highest_social_fit" {{ ($filters['sort'] ?? '') == 'highest_social_fit' ? 'selected' : '' }}>Sort: Highest Social Fit</option>
                    <option value="newest" {{ ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' }}>Sort: Newest Added</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <!-- Opportunity Cards Grid -->
    <div class="row g-4">
        @forelse($opportunities as $opp)
            @php $p = $opp->product; $score = $p->score; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card-custom p-4 h-100 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-secondary">{{ $p->network->name ?? 'Network' }}</span>
                        <div class="d-flex align-items-center gap-1">
                            @if($opp->isStale)
                                <span class="badge bg-warning text-dark" title="Analysis > 30 days old"><i class="bi bi-exclamation-circle me-1"></i>Stale</span>
                            @endif
                            <span class="badge {{ $score ? $score->badgeClass() : 'bg-primary' }}">{{ $opp->recommendation }}</span>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-1">
                        <a href="{{ route('products.show', $p->id) }}" class="text-dark text-decoration-none">{{ $p->name }}</a>
                    </h5>
                    <p class="text-muted small mb-3">{{ $p->category }} | Commission: <strong class="text-success">{{ $p->commission_value }}{{ $p->commission_type === 'percentage' ? '%' : ' $' }}</strong></p>

                    <div class="bg-light rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Overall Opportunity Score</span>
                            <span class="fw-bold text-primary fs-5">{{ $opp->overallScore }}/100</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $opp->overallScore }}%;"></div>
                        </div>

                        <div class="d-flex justify-content-between small text-muted">
                            <span>Demand: <strong>{{ $opp->demandScore }}</strong></span>
                            <span>Buyer Intent: <strong>{{ $opp->buyerIntentScore }}</strong></span>
                            <span>Social: <strong>{{ $opp->socialFitScore }}</strong></span>
                        </div>
                    </div>

                    <div class="mt-auto border-top pt-3 d-flex align-items-center justify-content-between">
                        <a href="{{ route('products.ask-ai-team', $p->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-robot me-1"></i> Ask AI Team
                        </a>
                        <a href="{{ route('products.show', $p->id) }}" class="btn btn-sm btn-primary">
                            View Analysis <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-muted fs-1 mb-3 d-block"></i>
                <h5>No High-Score Opportunities Found</h5>
                <p class="text-muted">Try clearing filters or running AI analysis on existing products in your catalog.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $opportunities->appends(request()->query())->links() }}
    </div>
</div>
@endsection
