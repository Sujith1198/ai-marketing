@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-heart-pulse-fill text-danger me-2"></i>System Health & Shared Hosting Diagnostics</h2>
    <p class="text-muted mb-0">Verify system readiness, cron configuration, database status, and storage permissions.</p>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">Server & Environment</h5>
            <div class="vstack gap-2">
                <div class="d-flex justify-content-between small"><span>PHP Version:</span> <strong>{{ $phpVersion }}</strong></div>
                <div class="d-flex justify-content-between small"><span>Laravel Version:</span> <strong>{{ $laravelVersion }}</strong></div>
                <div class="d-flex justify-content-between small"><span>Database Status:</span> <strong class="{{ $dbOk ? 'text-success' : 'text-danger' }}">{{ $dbStatus }}</strong></div>
                <div class="d-flex justify-content-between small"><span>Storage Writable:</span> <strong class="{{ $storageWritable ? 'text-success' : 'text-danger' }}">{{ $storageWritable ? 'Writable' : 'Read Only' }}</strong></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">cPanel Cron Setup Instructions</h5>
            <p class="small text-muted mb-2">Add the following single command to your cPanel Cron Jobs (Every Minute):</p>
            <div class="p-3 bg-dark text-light rounded font-monospace extra-small mb-3">
                * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
            </div>
            <span class="badge bg-success">No Redis or Supervisor Required!</span>
        </div>
    </div>
</div>
@endsection
