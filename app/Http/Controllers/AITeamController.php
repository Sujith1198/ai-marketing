<?php

namespace App\Http\Controllers;

use App\Models\AIAgent;
use App\Models\AIAgentRun;
use App\Models\AIProvider;
use Illuminate\Http\Request;

class AITeamController extends Controller
{
    public function index()
    {
        $agents = AIAgent::with('provider')->orderBy('priority', 'asc')->get();
        $providers = AIProvider::all();
        $recentRuns = AIAgentRun::with('agent')->orderBy('created_at', 'desc')->take(10)->get();

        return view('ai_team.index', compact('agents', 'providers', 'recentRuns'));
    }

    public function edit(AIAgent $agent)
    {
        $providers = AIProvider::all();
        return view('ai_team.edit', compact('agent', 'providers'));
    }

    public function update(Request $request, AIAgent $agent)
    {
        $request->validate([
            'system_prompt' => ['required', 'string'],
            'ai_provider_id' => ['nullable', 'exists:ai_providers,id'],
            'model_override' => ['nullable', 'string'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['required', 'integer', 'min:100', 'max:8192'],
            'is_enabled' => ['boolean'],
        ]);

        $agent->update([
            'system_prompt' => $request->input('system_prompt'),
            'ai_provider_id' => $request->input('ai_provider_id'),
            'model_override' => $request->input('model_override'),
            'temperature' => $request->input('temperature'),
            'max_tokens' => $request->input('max_tokens'),
            'is_enabled' => $request->boolean('is_enabled', true),
        ]);

        return redirect()->route('ai-team.index')->with('success', "Agent '{$agent->name}' updated.");
    }

    public function toggleStatus(AIAgent $agent)
    {
        $agent->update(['is_enabled' => !$agent->is_enabled]);
        $status = $agent->is_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "Agent '{$agent->name}' has been {$status}.");
    }
}
