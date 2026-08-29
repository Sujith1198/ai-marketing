@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-gear text-primary me-2"></i>Global System Settings</h2>
    <p class="text-muted mb-0">Configure default currencies, disclosures, human approval requirements, and product scoring engine weights.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error_scoring'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error_scoring') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- General Settings -->
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <h4 class="fw-bold mb-3"><i class="bi bi-sliders text-primary me-2"></i>Platform Config</h4>
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="app_name" class="form-label fw-semibold">Application Platform Name</label>
                    <input type="text" class="form-control" name="app_name" id="app_name" value="{{ $settings['app_name'] ?? 'AI Marketing Team' }}" required>
                </div>

                <div class="mb-3">
                    <label for="default_currency" class="form-label fw-semibold">Default Currency</label>
                    <input type="text" class="form-control" name="default_currency" id="default_currency" value="{{ $settings['default_currency'] ?? 'USD' }}" required>
                </div>

                <div class="mb-3">
                    <label for="default_disclosure" class="form-label fw-semibold">Default FTC Affiliate Disclosure Statement</label>
                    <textarea class="form-control" name="default_disclosure" id="default_disclosure" rows="3" required>{{ $settings['default_disclosure'] ?? 'Disclosure: This post contains affiliate links.' }}</textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="require_human_approval" value="true" id="require_human_approval" {{ ($settings['require_human_approval'] ?? 'true') === 'true' ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-primary" for="require_human_approval">
                        Strict Human-in-the-Loop Approval Required
                    </label>
                    <div class="form-text">No campaign or social post can be scheduled without CEO approval.</div>
                </div>

                <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Platform Settings</button>
            </form>
        </div>
    </div>

    <!-- Product Scoring Engine Weights -->
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold mb-0"><i class="bi bi-calculator-fill text-success me-2"></i>Product Scoring Weights</h4>
                <span class="badge bg-primary fs-6" id="weightsTotalBadge">Total: 100%</span>
            </div>

            <p class="small text-muted mb-3">Adjust positive metric weights. Total weight sum must equal exactly <strong>100%</strong>.</p>

            <form action="{{ route('settings.scoring.update') }}" method="POST" id="scoringWeightsForm">
                @csrf
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Demand Weight (%)</label>
                        <input type="number" name="weights[demand]" class="form-control weight-input" value="{{ $weights['demand'] ?? 15 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Buyer Intent (%)</label>
                        <input type="number" name="weights[buyer_intent]" class="form-control weight-input" value="{{ $weights['buyer_intent'] ?? 15 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Commission (%)</label>
                        <input type="number" name="weights[commission]" class="form-control weight-input" value="{{ $weights['commission'] ?? 10 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Content Potential (%)</label>
                        <input type="number" name="weights[content_potential]" class="form-control weight-input" value="{{ $weights['content_potential'] ?? 15 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Social Fit (%)</label>
                        <input type="number" name="weights[social_fit]" class="form-control weight-input" value="{{ $weights['social_fit'] ?? 10 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">SEO Opportunity (%)</label>
                        <input type="number" name="weights[seo_potential]" class="form-control weight-input" value="{{ $weights['seo_potential'] ?? 10 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Conversion Potential (%)</label>
                        <input type="number" name="weights[conversion_potential]" class="form-control weight-input" value="{{ $weights['conversion_potential'] ?? 15 }}" min="0" max="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Trust Score (%)</label>
                        <input type="number" name="weights[trust]" class="form-control weight-input" value="{{ $weights['trust'] ?? 10 }}" min="0" max="100" required>
                    </div>
                </div>

                <h6 class="fw-bold text-danger mt-3 mb-2">Penalty Deductions</h6>
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Max Competition Penalty</label>
                        <input type="number" name="max_competition_penalty" class="form-control" value="{{ $maxCompetitionPenalty ?? 15 }}" min="0" max="30">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Max Risk Penalty</label>
                        <input type="number" name="max_risk_penalty" class="form-control" value="{{ $maxRiskPenalty ?? 20 }}" min="0" max="40">
                    </div>
                </div>

                <button type="submit" class="btn btn-success px-4 fw-semibold" id="saveWeightsBtn">Save Scoring Weights</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const weightInputs = document.querySelectorAll('.weight-input');
    const badge = document.getElementById('weightsTotalBadge');
    const saveBtn = document.getElementById('saveWeightsBtn');

    function calculateTotal() {
        let sum = 0;
        weightInputs.forEach(input => {
            sum += parseInt(input.value || 0, 10);
        });

        badge.textContent = `Total: ${sum}%`;
        if (sum === 100) {
            badge.className = 'badge bg-success fs-6';
            saveBtn.disabled = false;
        } else {
            badge.className = 'badge bg-danger fs-6';
            saveBtn.disabled = true;
        }
    }

    weightInputs.forEach(input => input.addEventListener('input', calculateTotal));
    calculateTotal();
});
</script>
@endsection
