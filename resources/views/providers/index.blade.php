@extends('layouts.app')

@section('title', 'AI Providers')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-cpu text-primary me-2"></i>AI Provider Architecture</h2>
    <p class="text-muted mb-0">Select primary AI provider, configure fallback providers, and verify live connectivity.</p>
</div>

<div class="row g-4">
    @foreach($providers as $provider)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">{{ $provider->name }}</h5>
                        @if($provider->is_primary)
                            <span class="badge bg-primary">PRIMARY</span>
                        @else
                            <span class="badge bg-secondary">FALLBACK</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <span class="text-muted extra-small">Default Model:</span>
                        <div class="fw-bold text-dark">{{ $provider->default_model }}</div>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted extra-small">API Secret Credential:</span>
                        <div class="fw-semibold text-dark">{{ optional($provider->credential)->masked_value ?? 'Using .env fallback' }}</div>
                    </div>

                    @if($provider->fallbackProvider)
                        <div class="mb-3">
                            <span class="text-muted extra-small">Fallback Provider:</span>
                            <div class="badge bg-info text-dark">{{ $provider->fallbackProvider->name }}</div>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-top vstack gap-2">
                    <form action="{{ route('providers.test', $provider->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-info btn-sm w-100 fw-semibold">
                            <i class="bi bi-plug-fill me-1"></i> Test Connection
                        </button>
                    </form>
                    <button class="btn btn-primary btn-sm w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#editProviderModal{{ $provider->id }}">
                        <i class="bi bi-pencil me-1"></i> Edit Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Provider Modal -->
        <div class="modal fade" id="editProviderModal{{ $provider->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('providers.update', $provider->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit {{ $provider->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="default_model{{ $provider->id }}" class="form-label fw-semibold">Default Model</label>
                                <input type="text" class="form-control" name="default_model" id="default_model{{ $provider->id }}" value="{{ $provider->default_model }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="credential_id{{ $provider->id }}" class="form-label fw-semibold">API Secret Vault Credential</label>
                                <select class="form-select" name="credential_id" id="credential_id{{ $provider->id }}">
                                    <option value="">Use .env key</option>
                                    @foreach($credentials as $cred)
                                        <option value="{{ $cred->id }}" {{ $provider->credential_id == $cred->id ? 'selected' : '' }}>
                                            {{ $cred->label }} ({{ $cred->masked_value }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="fallback_provider_id{{ $provider->id }}" class="form-label fw-semibold">Fallback AI Provider</label>
                                <select class="form-select" name="fallback_provider_id" id="fallback_provider_id{{ $provider->id }}">
                                    <option value="">None</option>
                                    @foreach($providers as $fProvider)
                                        @if($fProvider->id != $provider->id)
                                            <option value="{{ $fProvider->id }}" {{ $provider->fallback_provider_id == $fProvider->id ? 'selected' : '' }}>
                                                {{ $fProvider->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="is_primary{{ $provider->id }}" {{ $provider->is_primary ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_primary{{ $provider->id }}">Set as Primary Provider</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Provider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
