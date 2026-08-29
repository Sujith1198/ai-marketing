@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Products</a>
        <h2 class="fw-bold mb-1">{{ $product->name }}</h2>
        <span class="badge bg-light text-dark border me-2">{{ optional($product->network)->name }}</span>
        <span class="text-muted small">{{ $product->category }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('products.analyze', $product->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-info"><i class="bi bi-cpu me-1"></i> Re-Analyze</button>
        </form>
        <a href="{{ route('campaigns.wizard', ['product_id' => $product->id]) }}" class="btn btn-primary">
            <i class="bi bi-rocket-takeoff me-1"></i> Create Campaign
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Product Overview & Scores -->
    <div class="col-lg-5">
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3">Product Overview</h5>
            <p class="text-muted">{{ $product->description }}</p>
            <div class="vstack gap-2 border-top pt-3">
                <div class="d-flex justify-content-between small"><span class="text-muted">Price:</span> <strong class="text-dark">${{ number_format($product->price, 2) }}</strong></div>
                <div class="d-flex justify-content-between small"><span class="text-muted">Commission:</span> <strong class="text-success">{{ $product->commission_value }} ({{ $product->commission_type }})</strong></div>
                <div class="d-flex justify-content-between small"><span class="text-muted">Original Link:</span> <a href="{{ $product->product_url }}" target="_blank" class="text-truncate" style="max-width: 200px;">{{ $product->product_url }}</a></div>
                <div class="d-flex justify-content-between small"><span class="text-muted">Affiliate Link:</span> <a href="{{ $product->affiliate_url }}" target="_blank" class="text-truncate" style="max-width: 200px;">{{ $product->affiliate_url }}</a></div>
            </div>
        </div>

        @if($product->score)
            <div class="card-custom p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">Opportunity Score</h5>
                    <span class="badge {{ $product->score->badgeClass() }}">{{ $product->score->recommendationLabel() }}</span>
                </div>
                <div class="display-4 fw-bold text-primary text-center mb-3">{{ $product->score->overall_opportunity_score }}/100</div>

                <div class="vstack gap-2">
                    <div class="d-flex justify-content-between small"><span>Demand:</span> <strong>{{ $product->score->demand_score }}/100</strong></div>
                    <div class="d-flex justify-content-between small"><span>Buyer Intent:</span> <strong>{{ $product->score->buyer_intent_score }}/100</strong></div>
                    <div class="d-flex justify-content-between small"><span>Content Fit:</span> <strong>{{ $product->score->content_potential_score }}/100</strong></div>
                    <div class="d-flex justify-content-between small"><span>SEO Potential:</span> <strong>{{ $product->score->seo_potential_score }}/100</strong></div>
                </div>
            </div>
        @endif
    </div>

    <!-- AI Analysis Details -->
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-stars text-primary me-2"></i>AI Deep Research Breakdown</h5>

            @if($product->analysis)
                <div class="vstack gap-3">
                    <div class="p-3 bg-light rounded border">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-graph-up me-1"></i> Market Demand & Intent:</div>
                        <p class="small text-muted mb-0">{{ $product->analysis->market_demand }}</p>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-people me-1"></i> Target Audience & Pain Points:</div>
                        <p class="small text-muted mb-0">{{ $product->analysis->target_audience }}</p>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-lightning-charge me-1"></i> Viral & Social Fit:</div>
                        <p class="small text-muted mb-0">{{ $product->analysis->viral_potential }}</p>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check me-1"></i> Compliance & Risk Factors:</div>
                        <p class="small text-muted mb-0">{{ $product->analysis->risk_factors }}</p>
                    </div>
                </div>
            @else
                <p class="text-muted">No AI research generated yet. Click Re-Analyze button above to generate.</p>
            @endif
        </div>
    </div>
</div>
@endsection
