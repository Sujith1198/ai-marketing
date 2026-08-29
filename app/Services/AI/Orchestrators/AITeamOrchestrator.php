<?php

namespace App\Services\AI\Orchestrators;

use App\Models\AIAgent;
use App\Models\AIAgentRun;
use App\Models\AITeamMeeting;
use App\Models\AITeamMessage;
use App\Services\AI\AIProviderManager;
use Illuminate\Support\Facades\Log;

class AITeamOrchestrator
{
    protected AIProviderManager $providerManager;
    protected const MAX_AGENT_EXECUTIONS = 8;

    public function __construct(AIProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    /**
     * Conduct an AI Team Meeting.
     */
    public function conductMeeting(string $userQuery, array $agentSlugs = []): AITeamMeeting
    {
        $meeting = AITeamMeeting::create([
            'title' => 'AI Strategy Meeting: ' . substr($userQuery, 0, 60),
            'user_query' => $userQuery,
            'status' => 'running',
            'user_decision' => 'pending',
        ]);

        // Record User initial message
        AITeamMessage::create([
            'ai_team_meeting_id' => $meeting->id,
            'sender_type' => 'user',
            'content' => $userQuery,
            'execution_order' => 1,
        ]);

        // Select participating agents
        if (empty($agentSlugs)) {
            $agents = AIAgent::where('is_enabled', true)
                ->where('slug', '!=', 'cmo-agent')
                ->orderBy('priority', 'asc')
                ->take(self::MAX_AGENT_EXECUTIONS - 1)
                ->get();
        } else {
            $agents = AIAgent::whereIn('slug', $agentSlugs)
                ->where('is_enabled', true)
                ->take(self::MAX_AGENT_EXECUTIONS - 1)
                ->get();
        }

        $agentReports = [];
        $order = 2;

        foreach ($agents as $agent) {
            $report = $this->runAgent($agent, $userQuery, $agentReports);
            $agentReports[$agent->name] = $report;

            AITeamMessage::create([
                'ai_team_meeting_id' => $meeting->id,
                'ai_agent_id' => $agent->id,
                'sender_type' => 'agent',
                'agent_role' => $agent->role,
                'content' => $report,
                'execution_order' => $order++,
            ]);
        }

        // Run CMO Agent for final synthesis
        $cmoAgent = AIAgent::where('slug', 'cmo-agent')->first() 
            ?? AIAgent::where('role', 'Chief Marketing Officer')->first();

        $cmoSummary = "CMO Recommendation based on team analysis.";
        $finalRecommendation = [];
        $confidenceScore = 85;
        $recommendedAction = "CREATE_CAMPAIGN";

        if ($cmoAgent) {
            $cmoPrompt = "User Query: {$userQuery}\n\nTeam Reports:\n";
            foreach ($agentReports as $agentName => $reportText) {
                $cmoPrompt .= "--- {$agentName} ---\n{$reportText}\n\n";
            }
            $cmoPrompt .= "As the Chief Marketing Officer, synthesize the team's input and provide a final JSON response formatted as:\n";
            $cmoPrompt .= "{\n  \"summary\": \"Executive summary...\",\n  \"confidence_score\": 88,\n  \"recommended_action\": \"CREATE_CAMPAIGN\",\n  \"strategy_bullets\": [\"Point 1\", \"Point 2\"]\n}";

            $provider = $this->providerManager->resolve($cmoAgent->provider);
            $cmoOutput = $provider->generateStructuredOutput($cmoPrompt);

            if (isset($cmoOutput['summary'])) {
                $cmoSummary = $cmoOutput['summary'];
                $confidenceScore = $cmoOutput['confidence_score'] ?? 85;
                $recommendedAction = $cmoOutput['recommended_action'] ?? 'CREATE_CAMPAIGN';
                $finalRecommendation = $cmoOutput;
            } else {
                $cmoSummary = $provider->generateText($cmoPrompt);
                $finalRecommendation = ['raw' => $cmoSummary];
            }

            AITeamMessage::create([
                'ai_team_meeting_id' => $meeting->id,
                'ai_agent_id' => $cmoAgent->id,
                'sender_type' => 'agent',
                'agent_role' => $cmoAgent->role,
                'content' => $cmoSummary,
                'structured_payload' => $finalRecommendation,
                'execution_order' => $order++,
            ]);
        }

        $meeting->update([
            'status' => 'completed',
            'cmo_summary' => $cmoSummary,
            'final_recommendation' => $finalRecommendation,
            'confidence_score' => $confidenceScore,
            'recommended_action' => $recommendedAction,
        ]);

        return $meeting;
    }

    protected function runAgent(AIAgent $agent, string $userQuery, array $previousReports): string
    {
        $startTime = now();
        $prompt = "System Role: {$agent->system_prompt}\n\nUser Question: {$userQuery}\n";
        
        if (!empty($previousReports)) {
            $prompt .= "\nContext from previous team members:\n";
            foreach ($previousReports as $name => $report) {
                $prompt .= "- {$name}: {$report}\n";
            }
        }

        $prompt .= "\nProvide your specialized analysis and recommendation concise and clear.";

        $provider = $this->providerManager->resolve($agent->provider);
        $response = $provider->generateText($prompt, [
            'temperature' => $agent->temperature,
            'max_tokens' => $agent->max_tokens,
            'model' => $agent->model_override,
        ]);

        AIAgentRun::create([
            'ai_agent_id' => $agent->id,
            'ai_provider_id' => $agent->ai_provider_id,
            'model_used' => $agent->model_override ?? $agent->provider->default_model ?? 'gemini-1.5-flash',
            'started_at' => $startTime,
            'completed_at' => now(),
            'status' => 'success',
            'prompt_tokens' => (int) (strlen($prompt) / 4),
            'completion_tokens' => (int) (strlen($response) / 4),
            'response_summary' => substr($response, 0, 255),
        ]);

        return $response;
    }
}
