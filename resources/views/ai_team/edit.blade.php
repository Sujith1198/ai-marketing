@extends('layouts.app')

@section('title', 'Edit AI Agent - ' . $agent->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('ai-team.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Back to AI Team</a>
    <h2 class="fw-bold mb-1">Edit Agent: {{ $agent->name }}</h2>
    <p class="text-muted mb-0">Modify system instructions, model provider, temperature, and tokens.</p>
</div>

<div class="card-custom p-4 max-w-700">
    <form action="{{ route('ai-team.update', $agent->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="system_prompt" class="form-label fw-semibold">System Instructions / Prompt</label>
            <textarea class="form-control" id="system_prompt" name="system_prompt" rows="6" required>{{ $agent->system_prompt }}</textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="ai_provider_id" class="form-label fw-semibold">AI Provider Override</label>
                <select class="form-select" id="ai_provider_id" name="ai_provider_id">
                    <option value="">Default (Global Settings)</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}" {{ $agent->ai_provider_id == $provider->id ? 'selected' : '' }}>
                            {{ $provider->name }} ({{ $provider->driver }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="model_override" class="form-label fw-semibold">Model Override</label>
                <input type="text" class="form-control" id="model_override" name="model_override" value="{{ $agent->model_override }}" placeholder="e.g. gemini-1.5-pro">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="temperature" class="form-label fw-semibold">Temperature (Creativity: 0.0 - 1.0)</label>
                <input type="number" step="0.05" class="form-control" id="temperature" name="temperature" value="{{ $agent->temperature }}" required>
            </div>

            <div class="col-md-6">
                <label for="max_tokens" class="form-label fw-semibold">Max Output Tokens</label>
                <input type="number" class="form-control" id="max_tokens" name="max_tokens" value="{{ $agent->max_tokens }}" required>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" value="1" {{ $agent->is_enabled ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_enabled">
                Agent Active & Ready for Meetings
            </label>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Agent Instructions</button>
    </form>
</div>
@endsection
