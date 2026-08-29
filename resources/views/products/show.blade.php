@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header Actions -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h2 class="fw-bold mb-0">{{ $product->name }}</h2>
                <span class="badge bg-secondary">{{ $product->network->name ?? 'Network' }}</span>
                <span class="badge {{ $product->status->badgeClass() }}">{{ $product->status->label() }}</span>
            </div>
            <p class="text-muted mb-0">Category: <strong>{{ $product->category }}</strong> | Brand: <strong>{{ $product->brand ?? 'N/A' }}</strong></p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('products.ask-ai-team', $product->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-robot me-1"></i> Ask AI Team
            </a>

            <form action="{{ route('products.analyze', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cpu-fill me-1"></i> {{ $product->analysis ? 'Analyze Again (v' . ($product->analysis->analysis_version + 1) . ')' : 'Analyze Product' }}
                </button>
            </form>

            <a href="{{ route('campaigns.wizard', ['product_id' => $product->id]) }}" class="btn btn-success fw-semibold">
                <i class="bi bi-magic me-1"></i> Create Campaign
            </a>

            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Data Completeness & AI Confidence Banner -->
    <div class="card-custom p-3 mb-4 bg-light border">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-semibold small"><i class="bi bi-shield-check text-info me-1"></i>Data Completeness</span>
                    <span class="fw-bold small">{{ $completeness['score'] }}% ({{ $completeness['label'] }})</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar {{ $completeness['score'] >= 80 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $completeness['score'] }}%;"></div>
                </div>
                @if(!empty($completeness['missing_fields']))
                    <small class="text-muted d-block mt-1">Missing: {{ implode(', ', $completeness['missing_fields']) }}</small>
                @endif
            </div>

            <div class="col-md-6 text-md-end">
                <span class="badge bg-white text-dark border p-2 me-2">
                    AI Confidence: <strong class="text-primary">{{ $product->analysis->confidence_score ?? 85 }}%</strong>
                </span>
                <a href="{{ route('products.analysis-history', $product->id) }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-clock-history me-1"></i> View Analysis History
                </a>
            </div>
        </div>
    </div>

    <!-- Opportunity Score & Sub-scores -->
    @php $score = $product->score; $analysis = $product->analysis; @endphp
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card-custom p-4 text-center h-100">
                <h6 class="text-muted fw-bold text-uppercase mb-2">Overall Opportunity Score</h6>
                <div class="display-3 fw-bold text-primary mb-2">{{ $score ? $score->overall_opportunity_score : 0 }}<span class="fs-4 text-muted">/100</span></div>
                
                @if($score)
                    <div class="mb-3">
                        <span class="badge {{ $score->badgeClass() }} fs-6 px-3 py-2">{{ $score->recommendation }}</span>
                    </div>
                @endif

                <p class="small text-muted mb-0">Calculated via PHP Weighted Math Engine with competition & risk penalty deductions.</p>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Score Breakdown</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Market Demand</span>
                            <span>{{ $score->demand_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-primary" style="width:{{ $score->demand_score ?? 0 }}%;"></div></div>

                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Buyer Intent</span>
                            <span>{{ $score->buyer_intent_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-info" style="width:{{ $score->buyer_intent_score ?? 0 }}%;"></div></div>

                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Content Potential</span>
                            <span>{{ $score->content_potential_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-success" style="width:{{ $score->content_potential_score ?? 0 }}%;"></div></div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Social Media Fit</span>
                            <span>{{ $score->social_fit_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-warning" style="width:{{ $score->social_fit_score ?? 0 }}%;"></div></div>

                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>SEO Opportunity</span>
                            <span>{{ $score->seo_potential_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-secondary" style="width:{{ $score->seo_potential_score ?? 0 }}%;"></div></div>

                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                            <span>Commission Score</span>
                            <span>{{ $score->commission_score ?? 0 }}/100</span>
                        </div>
                        <div class="progress mb-3" style="height:6px;"><div class="progress-bar bg-success" style="width:{{ $score->commission_score ?? 0 }}%;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Target Audience & Pain Points</h5>
                
                <h6 class="fw-semibold text-dark">Target Audiences:</h6>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @forelse((array)($analysis->target_audience ?? []) as $aud)
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $aud }}</span>
                    @empty
                        <span class="text-muted small">Run analysis to extract audience segments.</span>
                    @endforelse
                </div>

                <h6 class="fw-semibold text-dark">Pain Points Solved:</h6>
                <ul class="mb-0 ps-3 text-muted">
                    @forelse((array)($analysis->pain_points ?? []) as $pp)
                        <li>{{ $pp }}</li>
                    @empty
                        <li>No pain points recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-exclamation text-warning me-2"></i>Compliance & Risk Notes</h5>
                
                <h6 class="fw-semibold text-dark">Compliance Requirements:</h6>
                <ul class="mb-3 ps-3 text-muted">
                    @forelse((array)($analysis->compliance_concerns ?? []) as $comp)
                        <li>{{ $comp }}</li>
                    @empty
                        <li>Standard FTC affiliate disclosure required.</li>
                    @endforelse
                </ul>

                <h6 class="fw-semibold text-dark">Affiliate Link:</h6>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ $product->affiliate_url }}" readonly>
                    <a href="{{ $product->affiliate_url }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Test</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
