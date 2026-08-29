@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Campaigns</a>
        <h2 class="fw-bold mb-1">{{ $campaign->name }}</h2>
        <span class="badge {{ $campaign->badgeClass() }}">{{ strtoupper(str_replace('_', ' ', $campaign->status)) }}</span>
        <span class="text-muted small ms-2">Product: <strong>{{ optional($campaign->product)->name }}</strong></span>
    </div>
    @if($campaign->status === 'pending_approval')
        <a href="{{ route('approvals.index') }}" class="btn btn-warning text-dark font-weight-bold">
            <i class="bi bi-check-circle me-1"></i> Review in Approval Center
        </a>
    @endif
</div>

<!-- Tabs Header -->
<ul class="nav nav-tabs mb-4" id="campaignTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">Overview</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="strategy-tab" data-bs-toggle="tab" data-bs-target="#strategy" type="button">AI Strategy</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button">Generated Content ({{ $campaign->contents->count() }})</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="prompts-tab" data-bs-toggle="tab" data-bs-target="#prompts" type="button">Creative Prompts</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">Schedule</button>
    </li>
</ul>

<div class="tab-content" id="campaignTabsContent">
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3">Campaign Summary</h5>
                    <div class="vstack gap-2">
                        <div class="d-flex justify-content-between small"><span class="text-muted">Goal:</span> <strong>{{ $campaign->goal }}</strong></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Affiliate Network:</span> <strong>{{ optional($campaign->network)->name }}</strong></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Platforms:</span> <strong>{{ implode(', ', $campaign->platforms) }}</strong></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Start Date:</span> <strong>{{ optional($campaign->start_date)->format('M d, Y') }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3">Target Product</h5>
                    <h6 class="fw-bold text-primary">{{ optional($campaign->product)->name }}</h6>
                    <p class="small text-muted mb-2">{{ optional($campaign->product)->description }}</p>
                    <a href="{{ route('products.show', $campaign->product_id) }}" class="btn btn-outline-primary btn-sm">View Product Analysis</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategy Tab -->
    <div class="tab-pane fade" id="strategy">
        @if($campaign->strategy)
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cpu-fill text-primary me-2"></i>AI Marketing Strategy Blueprint</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="fw-bold text-dark mb-1">Awareness Stage:</div>
                            <p class="mb-0 text-muted">{{ $campaign->strategy->awareness_stage }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <div class="fw-bold text-dark mb-1">Customer Persona:</div>
                            <p class="mb-0 text-muted">{{ json_encode($campaign->strategy->customer_persona) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Primary Hooks Generated:</h6>
                    <ul class="list-group">
                        @foreach($campaign->strategy->primary_hooks ?? [] as $hook)
                            <li class="list-group-item"><i class="bi bi-lightning-fill text-warning me-2"></i> "{{ $hook }}"</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="card-custom p-4 text-muted">No strategy blueprint generated yet.</div>
        @endif
    </div>

    <!-- Content Tab -->
    <div class="tab-pane fade" id="content">
        <div class="row g-4">
            @foreach($campaign->contents as $content)
                <div class="col-md-6">
                    <div class="card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-primary text-uppercase">{{ $content->platform }}</span>
                            <span class="badge {{ $content->badgeClass() }}">{{ $content->status }}</span>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $content->title }}</h5>
                        <p class="small text-muted mb-3" style="white-space: pre-line;">{{ $content->body_text }}</p>

                        @if($content->script)
                            <div class="p-2 bg-light rounded border extra-small mb-3">
                                <strong>Video Script:</strong> {{ $content->script }}
                            </div>
                        @endif

                        <div class="text-muted extra-small">
                            <strong>CTA:</strong> {{ $content->call_to_action }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Prompts Tab -->
    <div class="tab-pane fade" id="prompts">
        <div class="row g-4">
            @foreach($campaign->creativePrompts as $prompt)
                <div class="col-md-6">
                    <div class="card-custom p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-info text-dark">{{ $prompt->platform }} ({{ $prompt->aspect_ratio }})</span>
                            <span class="extra-small text-muted">{{ $prompt->recommended_tool }}</span>
                        </div>
                        <h6 class="fw-bold">Visual Prompt:</h6>
                        <p class="small bg-light p-3 rounded border">{{ $prompt->prompt_text }}</p>
                        <span class="extra-small text-muted">Text Overlay: <strong>"{{ $prompt->suggested_text_overlay }}"</strong></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Schedule Tab -->
    <div class="tab-pane fade" id="schedule">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">Scheduled Social Posts</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Platform</th>
                            <th>Content Title</th>
                            <th>Scheduled Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaign->scheduledPosts as $post)
                            <tr>
                                <td><span class="badge bg-secondary text-uppercase">{{ $post->platform }}</span></td>
                                <td>{{ optional($post->content)->title }}</td>
                                <td>{{ optional($post->scheduled_at)->format('M d, Y H:i A') }}</td>
                                <td><span class="badge {{ $post->badgeClass() }}">{{ $post->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">Posts will be scheduled automatically once the CEO approves the campaign!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
