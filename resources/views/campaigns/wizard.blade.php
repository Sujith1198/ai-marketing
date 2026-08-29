@extends('layouts.app')

@section('title', 'Campaign Creation Wizard')

@section('content')
<div class="mb-4">
    <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Campaigns</a>
    <h2 class="fw-bold mb-1"><i class="bi bi-magic text-primary me-2"></i>Campaign Creation Wizard</h2>
    <p class="text-muted mb-0">Follow the 8-step AI workflow to research, strategize, generate content, and submit for CEO approval.</p>
</div>

<!-- Progress Stepper Header -->
<div class="card-custom p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between text-center overflow-auto py-2">
        <div class="px-3"><span class="badge bg-primary rounded-circle me-1">1</span> <span class="fw-semibold small">Product</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-primary rounded-circle me-1">2</span> <span class="fw-semibold small">Platforms</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-primary rounded-circle me-1">3</span> <span class="fw-semibold small">Goal</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-secondary rounded-circle me-1">4</span> <span class="text-muted small">AI Strategy</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-secondary rounded-circle me-1">5</span> <span class="text-muted small">Content</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-secondary rounded-circle me-1">6</span> <span class="text-muted small">Compliance</span></div>
        <i class="bi bi-chevron-right text-muted"></i>
        <div class="px-3"><span class="badge bg-secondary rounded-circle me-1">7</span> <span class="text-muted small">Approval</span></div>
    </div>
</div>

<div class="card-custom p-4 max-w-700">
    <form action="{{ route('campaigns.wizard.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="product_id" class="form-label fw-bold">Step 1: Select Affiliate Product</label>
            <select class="form-select form-select-lg" id="product_id" name="product_id" required>
                <option value="">-- Choose Analyzed Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $selectedProductId == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} (Score: {{ optional($product->score)->overall_opportunity_score ?? 'N/A' }}/100)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="name" class="form-label fw-bold">Campaign Name</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Summer Hosting Blitz 2026">
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Step 2: Choose Target Marketing Platforms</label>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="form-check card p-3 text-center border">
                        <input class="form-check-input mx-auto mb-2" type="checkbox" name="platforms[]" value="instagram" id="plat_insta" checked>
                        <label class="form-check-label fw-semibold" for="plat_insta"><i class="bi bi-instagram text-danger d-block fs-4 mb-1"></i> Instagram</label>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="form-check card p-3 text-center border">
                        <input class="form-check-input mx-auto mb-2" type="checkbox" name="platforms[]" value="pinterest" id="plat_pin" checked>
                        <label class="form-check-label fw-semibold" for="plat_pin"><i class="bi bi-pinterest text-danger d-block fs-4 mb-1"></i> Pinterest</label>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="form-check card p-3 text-center border">
                        <input class="form-check-input mx-auto mb-2" type="checkbox" name="platforms[]" value="youtube" id="plat_yt" checked>
                        <label class="form-check-label fw-semibold" for="plat_yt"><i class="bi bi-youtube text-danger d-block fs-4 mb-1"></i> YouTube</label>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="form-check card p-3 text-center border">
                        <input class="form-check-input mx-auto mb-2" type="checkbox" name="platforms[]" value="facebook" id="plat_fb">
                        <label class="form-check-label fw-semibold" for="plat_fb"><i class="bi bi-facebook text-primary d-block fs-4 mb-1"></i> Facebook</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="goal" class="form-label fw-bold">Step 3: Campaign Goal & Angle</label>
            <input type="text" class="form-control" id="goal" name="goal" placeholder="e.g. Drive 50 sales targeting freelancers needing cheap portfolio hosting">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="start_date" class="form-label fw-semibold">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-6">
                <label for="end_date" class="form-label fw-semibold">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ now()->addDays(30)->toDateString() }}">
            </div>
        </div>

        <div class="p-3 bg-light rounded border mb-4">
            <div class="d-flex align-items-center gap-2 text-primary fw-semibold mb-1">
                <i class="bi bi-cpu-fill"></i> What Happens Next (Automated AI Engine):
            </div>
            <ul class="small text-muted mb-0 ps-3">
                <li>AI Team generates customer personas, hooks, angles, and content pillars.</li>
                <li>Copywriter & Creative Agents draft platform-specific posts & image generation prompts.</li>
                <li>Compliance Agent inspects disclosures & policy safety.</li>
                <li>Submits completed campaign to CEO Approval Center!</li>
            </ul>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> Generate Strategy & Submit for Approval
        </button>
    </form>
</div>
@endsection
