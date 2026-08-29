@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-gear text-primary me-2"></i>Global System Settings</h2>
    <p class="text-muted mb-0">Configure default currencies, disclosures, human approval requirements, and AI defaults.</p>
</div>

<div class="card-custom p-4 max-w-700">
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
                Strict Human-in-the-Loop Approval Required (REQUIRE_HUMAN_APPROVAL = TRUE)
            </label>
            <div class="form-text">When checked, no campaign or social post can be scheduled or published automatically without explicit CEO approval.</div>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Global Settings</button>
    </form>
</div>
@endsection
