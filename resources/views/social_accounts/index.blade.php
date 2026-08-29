@extends('layouts.app')

@section('title', 'Social Accounts')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1">Social Accounts Integration</h2>
    <p class="text-muted mb-0">Connect your Instagram, Facebook, Pinterest, and YouTube accounts via secure OAuth.</p>
</div>

<div class="row g-4">
    @foreach($platforms as $platform)
        <div class="col-md-6 col-lg-3">
            <div class="card-custom p-4 text-center">
                <i class="bi bi-{{ strtolower($platform->slug) === 'youtube' ? 'youtube' : (strtolower($platform->slug) === 'pinterest' ? 'pinterest' : (strtolower($platform->slug) === 'instagram' ? 'instagram' : 'facebook')) }} display-3 text-primary mb-3"></i>
                <h5 class="fw-bold mb-1">{{ $platform->name }}</h5>
                <span class="badge bg-success mb-3">OAuth Supported</span>
                <a href="{{ route('social-accounts.connect', $platform->id) }}" class="btn btn-outline-primary btn-sm d-block fw-semibold">
                    <i class="bi bi-link-45deg me-1"></i> Connect Account
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
