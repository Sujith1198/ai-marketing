@extends('layouts.app')

@section('title', 'AI Opportunity Center')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-stars text-primary me-2"></i>AI Opportunity Center</h2>
        <p class="text-muted mb-0">Affiliate products evaluated and scored across 11 demand & intent factors by AI Agents.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Product for AI Analysis</a>
</div>

<!-- Opportunity Cards Grid -->
<div class="row g-4">
    @forelse($scores as $score)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="badge bg-light text-dark border">{{ optional($score->product->network)->name }}</span>
                        <span class="badge {{ $score->badgeClass() }}">{{ $score->recommendationLabel() }}</span>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $score->product->name }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit($score->product->description, 90) }}</p>

                    <!-- Score Meters -->
                    <div class="vstack gap-2 mb-3 bg-light p-3 rounded border">
                        <div class="d-flex align-items-center justify-content-between small">
                            <span>Demand Score</span>
                            <span class="fw-bold">{{ $score->demand_score }}/100</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $score->demand_score }}%;"></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between small">
                            <span>Buyer Intent</span>
                            <span class="fw-bold">{{ $score->buyer_intent_score }}/100</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $score->buyer_intent_score }}%;"></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between small">
                            <span>Commission Rate</span>
                            <span class="fw-bold">{{ $score->commission_score }}/100</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $score->commission_score }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small">Overall Score</span>
                        <div class="h4 fw-bold mb-0 text-primary">{{ $score->overall_opportunity_score }}/100</div>
                    </div>
                    <a href="{{ route('campaigns.wizard', ['product_id' => $score->product->id]) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-rocket-takeoff me-1"></i> Create Campaign
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <h5>No opportunity scores available.</h5>
            <p>Add products to start automatic AI scoring!</p>
        </div>
    @endforelse
</div>
@endsection
