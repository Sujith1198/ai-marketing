@extends('layouts.app')

@section('title', 'AI Marketing Team')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1">AI Marketing Organization</h2>
        <p class="text-muted mb-0">View, configure, and instruct your specialized AI Agents.</p>
    </div>
    <a href="{{ route('ai-team.chat') }}" class="btn btn-primary">
        <i class="bi bi-chat-dots-fill me-1"></i> Call Team Meeting
    </a>
</div>

<div class="row g-4">
    @foreach($agents as $agent)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-workspace fs-3 text-primary"></i>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $agent->name }}</h5>
                                <span class="text-muted small">{{ $agent->role }}</span>
                            </div>
                        </div>
                        <form action="{{ route('ai-team.toggle', $agent->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $agent->is_enabled ? 'btn-success' : 'btn-secondary' }}">
                                {{ $agent->is_enabled ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </div>

                    <p class="text-muted small mb-3">{{ $agent->description }}</p>

                    <div class="bg-light p-2 rounded mb-3 border">
                        <span class="text-uppercase extra-small text-muted fw-semibold">System Prompt Preview:</span>
                        <p class="small mb-0 text-truncate" style="max-height: 40px;">{{ $agent->system_prompt }}</p>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small text-muted">Provider: <strong>{{ optional($agent->provider)->name ?? 'Default Gemini' }}</strong></span>
                    <a href="{{ route('ai-team.edit', $agent->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Agent
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
