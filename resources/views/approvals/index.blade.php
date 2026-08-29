@extends('layouts.app')

@section('title', 'Human Approval Center')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-shield-check text-primary me-2"></i>Human Approval Center</h2>
        <p class="text-muted mb-0">The CEO is the final decision maker. Review AI recommendations and campaign drafts before scheduling.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark fs-6">{{ $pendingApprovals->total() }} Pending</span>
        <span class="badge bg-success fs-6">{{ $approvedCount }} Approved</span>
        <span class="badge bg-danger fs-6">{{ $rejectedCount }} Rejected</span>
    </div>
</div>

<div class="card-custom p-4">
    <form action="{{ route('approvals.bulk-approve') }}" method="POST" id="bulkApproveForm">
        @csrf
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label fw-semibold" for="selectAll">Select All Pending Items</label>
            </div>
            <button type="submit" class="btn btn-success btn-sm fw-semibold">
                <i class="bi bi-check-all me-1"></i> Bulk Approve Selected
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Type</th>
                        <th>Requested Item</th>
                        <th>AI Confidence</th>
                        <th>Risk Level</th>
                        <th>Requested At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingApprovals as $approval)
                        <tr>
                            <td>
                                <input class="form-check-input approval-checkbox" type="checkbox" name="approval_ids[]" value="{{ $approval->id }}">
                            </td>
                            <td>
                                <span class="badge bg-primary text-uppercase">{{ $approval->approval_type }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    @if($approval->approvable)
                                        {{ $approval->approvable->name ?? $approval->approvable->title ?? 'Item #' . $approval->approvable_id }}
                                    @else
                                        Approval #{{ $approval->id }}
                                    @endif
                                </div>
                                <span class="extra-small text-muted">{{ $approval->notes }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                        <div class="progress-bar bg-success" style="width: {{ $approval->ai_confidence }}%;"></div>
                                    </div>
                                    <span class="fw-bold small">{{ $approval->ai_confidence }}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">Safe</span>
                            </td>
                            <td>{{ $approval->requested_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <form action="{{ route('approvals.approve', $approval->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-end-0">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $approval->id }}">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $approval->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('approvals.reject', $approval->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Reject Approval Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="notes{{ $approval->id }}" class="form-label fw-semibold">Reason for Rejection</label>
                                                <textarea class="form-control" name="notes" id="notes{{ $approval->id }}" rows="3" required placeholder="State feedback for AI revision..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                <h5>All items reviewed!</h5>
                                <p class="mb-0">There are no pending approvals requiring action right now.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-3">
        {{ $pendingApprovals->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.approval-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection
