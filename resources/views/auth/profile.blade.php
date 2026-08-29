@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1">CEO Profile & Password Settings</h2>
    <p class="text-muted mb-0">Update your name, email, and password credentials.</p>
</div>

<div class="card-custom p-4 max-w-700">
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">CEO Name</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input type="email" class="form-control" name="email" id="email" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3 border-top pt-3">
            <label for="current_password" class="form-label fw-semibold">Current Password (Required for password change)</label>
            <input type="password" class="form-control" name="current_password" id="current_password">
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="new_password" class="form-label fw-semibold">New Password</label>
                <input type="password" class="form-control" name="new_password" id="new_password">
            </div>
            <div class="col-md-6">
                <label for="new_password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation">
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Update Profile</button>
    </form>
</div>
@endsection
