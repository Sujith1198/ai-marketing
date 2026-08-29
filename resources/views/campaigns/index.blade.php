@extends('layouts.app')

@section('title', 'Campaigns Manager')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1">Marketing Campaigns</h2>
        <p class="text-muted mb-0">Track AI strategy campaigns, content generation, and approval statuses.</p>
    </div>
    <a href="{{ route('campaigns.wizard') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Launch Campaign Wizard
    </a>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Campaign Name</th>
                    <th>Product</th>
                    <th>Network</th>
                    <th>Target Platforms</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $campaign->name }}</div>
                            <span class="extra-small text-muted">{{ $campaign->goal }}</span>
                        </td>
                        <td>{{ optional($campaign->product)->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ optional($campaign->network)->name }}</span></td>
                        <td>
                            @foreach($campaign->platforms as $p)
                                <span class="badge bg-secondary text-uppercase extra-small me-1">{{ $p }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $campaign->badgeClass() }}">{{ strtoupper(str_replace('_', ' ', $campaign->status)) }}</span>
                        </td>
                        <td>{{ optional($campaign->start_date)->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('campaigns.show', $campaign->id) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No campaigns created yet. Click 'Launch Campaign Wizard' to start!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
