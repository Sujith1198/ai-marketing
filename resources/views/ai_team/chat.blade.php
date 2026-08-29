@extends('layouts.app')

@section('title', 'AI Team Strategy Meeting')

@section('content')
<div class="row g-4">
    <!-- Meeting List Sidebar -->
    <div class="col-lg-4">
        <div class="card-custom p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>AI Meetings</h5>
                <a href="{{ route('ai-team.chat') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> New Meeting</a>
            </div>

            <div class="list-group list-group-flush overflow-auto" style="max-height: 600px;">
                @forelse($meetings as $meeting)
                    <a href="{{ route('ai-team.chat.show', $meeting->id) }}" class="list-group-item list-group-item-action py-3 rounded mb-2 border {{ (isset($activeMeeting) && $activeMeeting->id == $meeting->id) ? 'bg-primary-subtle text-primary border-primary' : '' }}">
                        <div class="fw-semibold text-truncate">{{ $meeting->title }}</div>
                        <span class="extra-small text-muted">{{ $meeting->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <p class="text-muted small py-3 text-center">No meetings held yet. Start your first AI strategy meeting!</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Meeting Chat Stream -->
    <div class="col-lg-8">
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Call an AI Team Strategy Meeting</h5>
            
            <form action="{{ route('ai-team.chat.start') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea class="form-control" name="query" rows="3" placeholder="e.g., What are the best affiliate products to promote for back-to-school season?" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-play-fill me-1"></i> Start Meeting & Get CMO Recommendation
                </button>
            </form>
        </div>

        @if(isset($activeMeeting))
            <div class="card-custom p-4">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $activeMeeting->title }}</h4>
                        <span class="badge bg-success">Status: Completed</span>
                        <span class="badge bg-info text-dark">Confidence: {{ $activeMeeting->confidence_score }}%</span>
                    </div>
                </div>

                <!-- Messages Stream -->
                <div class="vstack gap-3 mb-4">
                    @foreach($activeMeeting->messages as $msg)
                        @if($msg->sender_type === 'user')
                            <div class="p-3 bg-light rounded border border-primary">
                                <div class="fw-bold text-primary mb-1"><i class="bi bi-person-fill me-1"></i> CEO Question:</div>
                                <p class="mb-0">{{ $msg->content }}</p>
                            </div>
                        @else
                            <div class="p-3 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-robot text-info fs-5"></i>
                                    <span class="fw-bold">{{ optional($msg->agent)->name ?? $msg->agent_role }}</span>
                                    <span class="badge bg-secondary extra-small">{{ $msg->agent_role }}</span>
                                </div>
                                <p class="mb-0 text-secondary" style="white-space: pre-line;">{{ $msg->content }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- CMO Final Recommendation Card -->
                @if($activeMeeting->cmo_summary)
                    <div class="card bg-primary-subtle border-primary p-3">
                        <h5 class="fw-bold text-primary mb-2"><i class="bi bi-award-fill me-2"></i>CMO Final Decision & Recommendation</h5>
                        <p class="mb-3">{{ $activeMeeting->cmo_summary }}</p>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('campaigns.wizard') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-rocket-takeoff me-1"></i> Turn Recommendation Into Campaign
                            </a>
                            <form action="{{ route('ai-team.chat.respond', $activeMeeting->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="decision" value="approved">
                                <button type="submit" class="btn btn-sm btn-outline-success">Approve Strategy</button>
                            </form>
                            <form action="{{ route('ai-team.chat.respond', $activeMeeting->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="decision" value="rejected">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
