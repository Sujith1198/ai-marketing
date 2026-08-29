@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>Affiliate Accounts</h2>
            <p class="text-muted mb-0">Manage connected affiliate network accounts, tracking IDs, and API credentials.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="bi bi-plus-lg me-1"></i> Connect Affiliate Account
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($accounts as $acc)
            <div class="col-md-6 col-lg-4">
                <div class="card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-primary fs-6">{{ $acc->network->name ?? 'Affiliate Network' }}</span>
                        <span class="badge {{ $acc->status->value === 'connected' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $acc->status->label() }}
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $acc->name }}</h5>
                    <p class="text-muted small mb-3">Tracking ID: <code>{{ $acc->tracking_id ?? 'Default Tag' }}</code></p>

                    <div class="border-top pt-3 mt-3 d-flex align-items-center justify-content-between">
                        <small class="text-muted">Last Tested: {{ $acc->last_tested_at ? $acc->last_tested_at->diffForHumans() : 'Never' }}</small>
                        
                        <div class="d-flex gap-2">
                            <form action="{{ route('affiliate-accounts.test', $acc->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-arrow-repeat me-1"></i> Test
                                </button>
                            </form>

                            <form action="{{ route('affiliate-accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Remove account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-wallet2 text-muted fs-1 mb-3 d-block"></i>
                <h5>No Affiliate Accounts Connected</h5>
                <p class="text-muted">Connect your affiliate tracking IDs or API keys to start tracking conversions.</p>
                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Your First Account
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('affiliate-accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Connect Affiliate Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Affiliate Network</label>
                        <select name="affiliate_network_id" class="form-select" required>
                            @foreach($networks as $net)
                                <option value="{{ $net->id }}">{{ $net->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Primary Amazon US Tag" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tracking ID / Associate Tag</label>
                        <input type="text" name="tracking_id" class="form-control" placeholder="e.g. aimarketing-20">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">API Credentials (Optional)</label>
                        <select name="credential_id" class="form-select">
                            <option value="">None (Manual Mode)</option>
                            @foreach($credentials as $cred)
                                <option value="{{ $cred->id }}">{{ $cred->name }} ({{ $cred->provider }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
