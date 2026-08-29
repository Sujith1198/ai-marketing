@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-journal-text text-primary me-2"></i>Activity & Audit Logs</h2>
    <p class="text-muted mb-0">System audit trail of CEO actions, agent runs, and credential updates.</p>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="fw-bold">{{ optional($log->user)->name ?? 'System' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $log->action }}</span></td>
                        <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                        <td><code>{{ $log->ip_address }}</code></td>
                        <td>{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No activity logs recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
