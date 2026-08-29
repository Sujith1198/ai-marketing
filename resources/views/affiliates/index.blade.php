@extends('layouts.app')

@section('title', 'Affiliate Networks')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1">Affiliate Network Management</h2>
    <p class="text-muted mb-0">Manage your affiliate account usernames, tracking IDs, portal URLs, and credentials.</p>
</div>

<div class="row g-4">
    @foreach($networks as $network)
        <div class="col-md-6 col-lg-4">
            <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">{{ $network->name }}</h5>
                        <span class="badge {{ $network->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $network->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted extra-small fw-semibold text-uppercase">Affiliate Username / ID:</span>
                        <div class="fw-bold text-primary">{{ $network->affiliate_username ?? 'Not Set' }}</div>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted extra-small fw-semibold text-uppercase">Tracking Tag / SubID:</span>
                        <div class="fw-bold text-dark">{{ $network->tracking_id ?? 'Not Set' }}</div>
                    </div>

                    @if($network->portal_url)
                        <div class="mb-3">
                            <span class="text-muted extra-small fw-semibold text-uppercase">Portal URL:</span>
                            <div>
                                <a href="{{ $network->portal_url }}" target="_blank" class="small text-decoration-none">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Open {{ $network->name }} Dashboard
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <span class="text-muted extra-small text-uppercase">Capabilities:</span>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($network->capabilities as $cap)
                                <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $cap) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <button class="btn btn-outline-primary btn-sm w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#editNetworkModal{{ $network->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Add / Edit Username & Link
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Network Modal -->
        <div class="modal fade" id="editNetworkModal{{ $network->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('affiliates.update', $network->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Configure {{ $network->name }} Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="affiliate_username{{ $network->id }}" class="form-label fw-semibold">Affiliate Username / Account ID</label>
                                <input type="text" class="form-control" name="affiliate_username" id="affiliate_username{{ $network->id }}" value="{{ $network->affiliate_username }}" placeholder="e.g. your_username_20">
                            </div>

                            <div class="mb-3">
                                <label for="tracking_id{{ $network->id }}" class="form-label fw-semibold">Affiliate Tracking Tag / SubID</label>
                                <input type="text" class="form-control" name="tracking_id" id="tracking_id{{ $network->id }}" value="{{ $network->tracking_id }}" placeholder="e.g. aimarketing-20">
                            </div>

                            <div class="mb-3">
                                <label for="portal_url{{ $network->id }}" class="form-label fw-semibold">Affiliate Dashboard Portal URL</label>
                                <input type="url" class="form-control" name="portal_url" id="portal_url{{ $network->id }}" value="{{ $network->portal_url }}" placeholder="https://affiliate-program.amazon.com">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $network->id }}" {{ $network->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="active{{ $network->id }}">Network Enabled</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-semibold">Save Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
