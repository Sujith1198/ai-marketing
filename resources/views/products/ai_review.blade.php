@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-robot text-primary me-2"></i>AI Team Product Review: {{ $product->name }}</h2>
            <p class="text-muted mb-0">Multi-agent opinions, strategic strengths, compliance assessment, and CMO decision.</p>
        </div>
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Product Detail
        </a>
    </div>

    <!-- CMO Final Decision Box -->
    <div class="card-custom p-4 mb-4 border-start border-4 border-success bg-light">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="badge bg-success mb-2">CMO Decision</span>
                <h4 class="fw-bold mb-1">{{ $review['cmo_decision'] }}</h4>
                <p class="text-muted mb-0">Best Target Audience: <strong>{{ $review['best_audience'] }}</strong></p>
            </div>
            <a href="{{ route('campaigns.wizard', ['product_id' => $product->id]) }}" class="btn btn-success btn-lg fw-semibold">
                <i class="bi bi-magic me-1"></i> Create Campaign Wizard
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold text-success mb-3"><i class="bi bi-hand-thumbs-up-fill me-2"></i>Main Strengths</h5>
                <ul class="mb-0 ps-3">
                    @foreach($review['main_strengths'] as $str)
                        <li class="mb-2">{{ $str }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Weaknesses & Risks</h5>
                <ul class="mb-0 ps-3">
                    @foreach($review['main_weaknesses'] as $weak)
                        <li class="mb-2">{{ $weak }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Agent Opinions Stream -->
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-chat-square-text-fill text-primary me-2"></i>Agent Roster Evaluations</h5>
        <div class="vstack gap-3">
            @foreach($review['agent_reviews'] as $rev)
                <div class="p-3 bg-white rounded border">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-primary"><i class="bi bi-cpu me-1"></i> {{ $rev['agent_name'] }}</span>
                        <span class="badge bg-light text-dark border">{{ $rev['role'] }}</span>
                    </div>
                    <p class="mb-0 text-muted small">{!! nl2br(e($rev['opinion'])) !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
