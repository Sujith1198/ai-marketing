<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Enums\CampaignStatus;
use App\Enums\PostStatus;
use App\Models\Approval;
use App\Models\Campaign;
use App\Models\CampaignContent;
use App\Models\ScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'campaigns');

        $pendingApprovals = Approval::with(['approvable', 'reviewer'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $approvedCount = Approval::where('status', 'approved')->count();
        $rejectedCount = Approval::where('status', 'rejected')->count();

        return view('approvals.index', compact('pendingApprovals', 'tab', 'approvedCount', 'rejectedCount'));
    }

    public function approve(Approval $approval, Request $request)
    {
        $approval->update([
            'status' => ApprovalStatus::APPROVED->value,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
            'notes' => $request->input('notes', 'Approved by CEO.'),
        ]);

        // Trigger underlying entity status change
        $this->processApprovedEntity($approval);

        return back()->with('success', 'Item approved successfully!');
    }

    public function reject(Approval $approval, Request $request)
    {
        $request->validate(['notes' => ['required', 'string']]);

        $approval->update([
            'status' => ApprovalStatus::REJECTED->value,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => Auth::id(),
            'notes' => $request->input('notes'),
        ]);

        $this->processRejectedEntity($approval);

        return back()->with('success', 'Item rejected.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'approval_ids' => ['required', 'array'],
            'approval_ids.*' => ['exists:approvals,id'],
        ]);

        $approvals = Approval::whereIn('id', $request->input('approval_ids'))->get();

        foreach ($approvals as $approval) {
            $approval->update([
                'status' => ApprovalStatus::APPROVED->value,
                'reviewed_at' => now(),
                'reviewed_by_user_id' => Auth::id(),
                'notes' => 'Bulk approved by CEO.',
            ]);
            $this->processApprovedEntity($approval);
        }

        return back()->with('success', count($approvals) . ' items bulk approved!');
    }

    protected function processApprovedEntity(Approval $approval)
    {
        $entity = $approval->approvable;

        if ($entity instanceof Campaign) {
            $entity->update(['status' => CampaignStatus::APPROVED->value]);
            
            // Create scheduled posts for all campaign contents
            foreach ($entity->contents as $content) {
                ScheduledPost::create([
                    'campaign_id' => $entity->id,
                    'campaign_content_id' => $content->id,
                    'platform' => $content->platform,
                    'scheduled_at' => now()->addHours(rand(2, 48)),
                    'status' => PostStatus::SCHEDULED->value,
                ]);
            }
        } elseif ($entity instanceof CampaignContent) {
            $entity->update(['status' => PostStatus::APPROVED->value]);
        }
    }

    protected function processRejectedEntity(Approval $approval)
    {
        $entity = $approval->approvable;

        if ($entity instanceof Campaign) {
            $entity->update(['status' => CampaignStatus::REJECTED->value]);
        } elseif ($entity instanceof CampaignContent) {
            $entity->update(['status' => PostStatus::CANCELLED->value]);
        }
    }
}
