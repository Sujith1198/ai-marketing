@extends('layouts.app')

@section('title', 'Secure API Key Vault')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Secure API Credential Vault</h2>
        <p class="text-muted mb-0">AES-256-GCM encrypted API key manager. Secret values are never exposed in HTML or plaintext.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCredentialModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Credential
    </button>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Provider Name</th>
                    <th>Label</th>
                    <th>Masked Value</th>
                    <th>Status</th>
                    <th>Last Tested</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($credentials as $cred)
                    <tr>
                        <td class="fw-bold">{{ $cred->provider_name }}</td>
                        <td>{{ $cred->label }}</td>
                        <td><code>{{ $cred->masked_value }}</code></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>{{ optional($cred->last_tested_at)->diffForHumans() }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#replaceModal{{ $cred->id }}">
                                Replace Key
                            </button>
                            <form action="{{ route('vault.destroy', $cred->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Revoke and delete credential?')">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Replace Credential Modal -->
                    <div class="modal fade" id="replaceModal{{ $cred->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('vault.replace', $cred->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Replace Secret Value</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small text-muted mb-3">Old secret value will be revoked and overwritten with AES-256-GCM encryption.</p>
                                        <div class="mb-3">
                                            <label for="new_secret_value{{ $cred->id }}" class="form-label fw-semibold">Enter New Secret Key</label>
                                            <input type="password" class="form-control" name="new_secret_value" id="new_secret_value{{ $cred->id }}" required autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Encrypt & Save New Secret</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No encrypted API keys stored in vault. Click 'Add New Credential' to add your keys!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Credential Modal -->
<div class="modal fade" id="addCredentialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vault.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Encrypted API Credential</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="provider_name" class="form-label fw-semibold">Provider / Service Name</label>
                        <input type="text" class="form-control" name="provider_name" id="provider_name" required placeholder="e.g. Gemini / Groq / Amazon">
                    </div>
                    <div class="mb-3">
                        <label for="label" class="form-label fw-semibold">Credential Label</label>
                        <input type="text" class="form-control" name="label" id="label" required placeholder="e.g. Production Gemini Key #1">
                    </div>
                    <div class="mb-3">
                        <label for="secret_value" class="form-label fw-semibold">API Secret Key Value</label>
                        <input type="password" class="form-control" name="secret_value" id="secret_value" required placeholder="Paste API Key here...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Encrypt & Save Secret</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
