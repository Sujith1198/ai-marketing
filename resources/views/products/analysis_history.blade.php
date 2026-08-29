@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Analysis History: {{ $product->name }}</h2>
            <p class="text-muted mb-0">Review past AI analysis runs, version trends, and score changes over time.</p>
        </div>
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Product Detail
        </a>
    </div>

    <div class="vstack gap-4">
        @forelse($analyses as $an)
            @php $score = $an->scores->first(); @endphp
            <div class="card-custom p-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <span class="badge bg-primary fs-6 me-2">Analysis Version v{{ $an->analysis_version }}</span>
                        <span class="text-muted small">Ran on {{ $an->created_at->format('M d, Y @ H:i') }}</span>
                    </div>
                    @if($score)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary fs-6">Score: {{ $score->overall_opportunity_score }}/100</span>
                            <span class="badge {{ $score->badgeClass() }} fs-6">{{ $score->recommendation }}</span>
                        </div>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Market Demand</h6>
                        <p class="small text-muted">{{ $an->market_demand }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Buyer Intent</h6>
                        <p class="small text-muted">{{ $an->buyer_intent }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Target Audiences</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach((array)$an->target_audience as $aud)
                                <span class="badge bg-light text-dark border">{{ $aud }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark">Compliance & Disclosure</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach((array)$an->compliance_concerns as $comp)
                                <span class="badge bg-warning text-dark">{{ $comp }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <p class="text-muted">No historical analysis records found for this product.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
