@extends('layouts.app')

@section('title', 'Connect ' . $platform->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('social-accounts.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Social Accounts
    </a>
    <h2 class="fw-bold mb-1">
        <i class="bi bi-{{ strtolower($platform->slug) === 'youtube' ? 'youtube' : (strtolower($platform->slug) === 'pinterest' ? 'pinterest' : (strtolower($platform->slug) === 'instagram' ? 'instagram' : 'facebook')) }} text-primary me-2"></i>
        Connect {{ $platform->name }} Account
    </h2>
    <p class="text-muted mb-0">Set up API credentials and authorize OAuth access for automated publishing.</p>
</div>

<div class="row g-4">
    <!-- OAuth Configuration Form -->
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">OAuth App Integration</h5>
            
            <form action="{{ route('social-accounts.index') }}" method="GET">
                <div class="mb-3">
                    <label for="account_name" class="form-label fw-semibold">Account / Handle Name</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" placeholder="e.g. @yourbrand_official" required>
                </div>

                <div class="mb-3">
                    <label for="client_id" class="form-label fw-semibold">{{ $platform->name }} App Client ID / App Key</label>
                    <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Enter App Client ID" required>
                </div>

                <div class="mb-3">
                    <label for="client_secret" class="form-label fw-semibold">{{ $platform->name }} App Client Secret</label>
                    <input type="password" class="form-control" id="client_secret" name="client_secret" placeholder="Stored securely in AES-256 Vault" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">OAuth Authorized Redirect URI</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light font-monospace small" id="redirect_uri" value="{{ url('/social-accounts/callback') }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('redirect_uri').value); alert('Redirect URI copied!');">
                            <i class="bi bi-clipboard me-1"></i> Copy
                        </button>
                    </div>
                    <div class="form-text">Copy and paste this URL into your {{ $platform->name }} Developer Console.</div>
                </div>

                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> Authorize & Connect {{ $platform->name }}
                </button>
            </form>
        </div>
    </div>

    <!-- Developer Setup Guide -->
    <div class="col-lg-5">
        <div class="card-custom p-4 bg-light border">
            <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Developer Setup Instructions</h5>
            
            @if(strtolower($platform->slug) === 'instagram' || strtolower($platform->slug) === 'facebook')
                <ol class="small text-muted vstack gap-2 ps-3 mb-0">
                    <li>Go to <a href="https://developers.facebook.com/" target="_blank">Meta for Developers</a> console.</li>
                    <li>Create an app with type <strong>Business</strong>.</li>
                    <li>Add <strong>Instagram Graph API</strong> and <strong>Facebook Login</strong> products.</li>
                    <li>Add the Redirect URI above to Facebook Login settings.</li>
                    <li>Copy App ID and App Secret into the form on the left.</li>
                </ol>
            @elseif(strtolower($platform->slug) === 'youtube')
                <ol class="small text-muted vstack gap-2 ps-3 mb-0">
                    <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>.</li>
                    <li>Enable <strong>YouTube Data API v3</strong>.</li>
                    <li>Create OAuth 2.0 Client Credentials.</li>
                    <li>Add Authorized Redirect URI listed on the left.</li>
                    <li>Copy Client ID & Secret into the form.</li>
                </ol>
            @elseif(strtolower($platform->slug) === 'pinterest')
                <ol class="small text-muted vstack gap-2 ps-3 mb-0">
                    <li>Go to <a href="https://developers.pinterest.com/" target="_blank">Pinterest Developers</a>.</li>
                    <li>Create an App in your Pinterest Business account.</li>
                    <li>Set Redirect URI to the URL listed on the left.</li>
                    <li>Request `boards:read,boards:write,pins:read,pins:write` scopes.</li>
                    <li>Copy App ID and Secret into the form.</li>
                </ol>
            @endif
        </div>
    </div>
</div>
@endsection
