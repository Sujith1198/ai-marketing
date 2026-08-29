@extends('layouts.app')

@section('title', 'CEO Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1">CEO Marketing Operating System</h2>
        <p class="text-muted mb-0">Overview of AI team recommendations, pending approvals, and affiliate performance.</p>
    </div>
    <a href="{{ route('ai-team.chat') }}" class="btn btn-primary">
        <i class="bi bi-chat-dots-fill me-1"></i> Start AI Team Meeting
    </a>
</div>

<!-- Key SaaS Performance Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Products Analyzed</span>
                <h3 class="fw-bold mb-0 mt-1">{{ $productsCount }}</h3>
            </div>
            <div class="metric-icon bg-primary-subtle text-primary">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Active Campaigns</span>
                <h3 class="fw-bold mb-0 mt-1">{{ $activeCampaignsCount }}</h3>
            </div>
            <div class="metric-icon bg-success-subtle text-success">
                <i class="bi bi-megaphone"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Pending Approvals</span>
                <h3 class="fw-bold mb-0 mt-1 text-warning">{{ $pendingApprovalsCount }}</h3>
            </div>
            <div class="metric-icon bg-warning-subtle text-warning">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold text-uppercase">Total Estimated Revenue</span>
                <h3 class="fw-bold mb-0 mt-1 text-success">${{ number_format($totalRevenue, 2) }}</h3>
            </div>
            <div class="metric-icon bg-info-subtle text-info">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Today's AI Opportunities Section -->
<div class="card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-stars text-primary me-2"></i>Today's Top AI Opportunities</h4>
            <p class="text-muted small mb-0">High-scoring affiliate products discovered and evaluated by AI Agents.</p>
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
                    <th>Viral Fit</th>
                    <th>Overall Score</th>
                    <th>Recommendation</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($opportunities as $score)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $score->product->name }}</div>
                            <span class="text-muted extra-small">{{ $score->product->category }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ optional($score->product->network)->name }}</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->demand_score }}/100</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->buyer_intent_score }}/100</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->commission_score }}/100</span></td>
                        <td><span class="fw-bold text-dark">{{ $score->viral_potential_score }}/100</span></td>
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
                        <td colspan="9" class="text-center py-4 text-muted">No AI opportunities analyzed yet. Click Products -> Add Product to analyze!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Urgent Approvals Needed -->
<div class="card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-exclamation-circle text-warning me-2"></i>Urgent Approvals Needed</h4>
        <a href="{{ route('approvals.index') }}" class="btn btn-outline-secondary btn-sm">Approval Center</a>
    </div>

    @if($pendingApprovals->count() > 0)
        <div class="list-group list-group-flush">
            @foreach($pendingApprovals as $approval)
                <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-0">
                    <div>
                        <div class="fw-semibold">{{ ucfirst($approval->approval_type) }} Approval Request #{{ $approval->id }}</div>
                        <p class="text-muted small mb-0">{{ $approval->notes }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('approvals.approve', $approval->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-outline-secondary">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted mb-0 py-2">No pending items requiring approval. All campaigns & content are up to date!</p>
    @endif
</div>
@endsection
