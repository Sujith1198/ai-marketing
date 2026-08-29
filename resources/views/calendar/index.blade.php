@extends('layouts.app')

@section('title', 'Content Calendar')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-calendar3 text-primary me-2"></i>Content Calendar</h2>
        <p class="text-muted mb-0">Scheduled social media posts awaiting automatic cron publishing.</p>
    </div>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Scheduled Date & Time</th>
                    <th>Platform</th>
                    <th>Campaign & Product</th>
                    <th>Content Title</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ optional($post->scheduled_at)->format('M d, Y') }}</div>
                            <span class="small text-muted">{{ optional($post->scheduled_at)->format('h:i A T') }}</span>
                        </td>
                        <td><span class="badge bg-primary text-uppercase">{{ $post->platform }}</span></td>
                        <td>
                            <div class="fw-semibold">{{ optional($post->campaign)->name }}</div>
                            <span class="extra-small text-muted">{{ optional(optional($post->campaign)->product)->name }}</span>
                        </td>
                        <td>{{ optional($post->content)->title }}</td>
                        <td><span class="badge {{ $post->badgeClass() }}">{{ strtoupper($post->status) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No posts scheduled yet. Approve campaigns in the Approval Center to populate your calendar!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
