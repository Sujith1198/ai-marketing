<?php

namespace App\Http\Controllers;

use App\Models\AIAgent;
use App\Models\AITeamMeeting;
use App\Services\AI\Orchestrators\AITeamOrchestrator;
use Illuminate\Http\Request;

class AITeamChatController extends Controller
{
    protected AITeamOrchestrator $orchestrator;

    public function __construct(AITeamOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function index()
    {
        $meetings = AITeamMeeting::with('messages.agent')->orderBy('created_at', 'desc')->get();
        $agents = AIAgent::where('is_enabled', true)->get();
        $activeMeeting = $meetings->first();

        return view('ai_team.chat', compact('meetings', 'agents', 'activeMeeting'));
    }

    public function show(AITeamMeeting $meeting)
    {
        $meeting->load('messages.agent');
        $meetings = AITeamMeeting::orderBy('created_at', 'desc')->get();
        $agents = AIAgent::where('is_enabled', true)->get();

        return view('ai_team.chat', [
            'meetings' => $meetings,
            'agents' => $agents,
            'activeMeeting' => $meeting,
        ]);
    }

    public function startMeeting(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:5'],
            'agent_slugs' => ['nullable', 'array'],
        ]);

        $meeting = $this->orchestrator->conductMeeting(
            $request->input('query'),
            $request->input('agent_slugs', [])
        );

        return redirect()->route('ai-team.chat.show', $meeting->id)->with('success', 'AI Team Meeting completed!');
    }

    public function respond(Request $request, AITeamMeeting $meeting)
    {
        $request->validate([
            'decision' => ['required', 'string', 'in:approved,rejected,follow_up'],
        ]);

        $meeting->update(['user_decision' => $request->input('decision')]);

        return back()->with('success', 'Decision recorded for this AI meeting.');
    }
}
