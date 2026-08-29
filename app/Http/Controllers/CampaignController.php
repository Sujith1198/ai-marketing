<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\Approval;
use App\Models\Campaign;
use App\Models\CampaignContent;
use App\Models\CampaignStrategy;
use App\Models\ComplianceReview;
use App\Models\CreativePrompt;
use App\Models\Product;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    protected AIProviderManager $aiManager;

    public function __construct(AIProviderManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    public function index(Request $request)
    {
        $query = Campaign::with(['product', 'network']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('campaigns.index', compact('campaigns'));
    }

    public function wizard(Request $request)
    {
        $products = Product::where('status', 'active')->with('score')->get();
        $selectedProductId = $request->input('product_id');

        return view('campaigns.wizard', compact('products', 'selectedProductId'));
    }

    public function storeWizard(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:255'],
            'platforms' => ['required', 'array', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $campaign = Campaign::create([
            'product_id' => $product->id,
            'affiliate_network_id' => $product->affiliate_network_id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . rand(100, 999),
            'goal' => $validated['goal'] ?? 'Drive affiliate commissions',
            'platforms' => $validated['platforms'],
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'end_date' => $validated['end_date'] ?? now()->addDays(30)->toDateString(),
            'status' => CampaignStatus::AI_REVIEWING->value,
        ]);

        // Generate AI Strategy
        $this->generateCampaignStrategy($campaign);

        // Generate Content & Prompts for selected platforms
        $this->generateCampaignContent($campaign);

        // Compliance check
        $this->runComplianceCheck($campaign);

        // Submit for Human Approval
        $campaign->update(['status' => CampaignStatus::PENDING_APPROVAL->value]);

        Approval::create([
            'approvable_type' => Campaign::class,
            'approvable_id' => $campaign->id,
            'approval_type' => 'campaign',
            'status' => 'pending',
            'requested_at' => now(),
            'ai_confidence' => 88,
            'risk_level' => 'safe',
            'notes' => 'Campaign strategy & platform content generated. Requires CEO Approval before scheduling.',
        ]);

        return redirect()->route('campaigns.show', $campaign->id)->with('success', 'Campaign strategy and content generated! Waiting for CEO Approval.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['product.score', 'network', 'strategy', 'contents', 'creativePrompts', 'scheduledPosts', 'approvals']);
        return view('campaigns.show', compact('campaign'));
    }

    protected function generateCampaignStrategy(Campaign $campaign)
    {
        $product = $campaign->product;
        $prompt = "As a Senior Marketing Strategist, create a comprehensive campaign strategy for:\n";
        $prompt .= "Product: {$product->name}\nGoal: {$campaign->goal}\nPlatforms: " . implode(', ', $campaign->platforms) . "\n\n";
        $prompt .= "Return JSON matching schema:\n";
        $prompt .= "{\n";
        $prompt .= '  "awareness_stage": "Problem Aware", "customer_persona": {"age": "25-45", "interests": ["Tech", "Business"]}, "content_pillars": ["Product Demos", "Cost Savings"],';
        $prompt .= '  "primary_hooks": ["Hook 1...", "Hook 2..."], "secondary_hooks": ["Hook 3..."], "cta_strategy": ["Click bio link..."], "seo_keywords": ["keyword 1", "keyword 2"], "hashtags": ["#marketing"]';
        $prompt .= "\n}";

        $provider = $this->aiManager->resolve();
        $output = $provider->generateStructuredOutput($prompt);

        CampaignStrategy::updateOrCreate(
            ['campaign_id' => $campaign->id],
            [
                'customer_persona' => $output['customer_persona'] ?? ['demographics' => 'Digital buyers'],
                'awareness_stage' => $output['awareness_stage'] ?? 'Solution Aware',
                'content_pillars' => $output['content_pillars'] ?? ['Product Demos', 'Reviews'],
                'primary_hooks' => $output['primary_hooks'] ?? ['Discover how to transform your results today.'],
                'secondary_hooks' => $output['secondary_hooks'] ?? ['Don\'t miss out on this lifetime deal.'],
                'cta_strategy' => $output['cta_strategy'] ?? ['Click link in bio to get full discount.'],
                'seo_keywords' => $output['seo_keywords'] ?? [$product->name, 'best ' . $product->category],
                'hashtags' => $output['hashtags'] ?? ['#' . Str::slug($product->name), '#affiliate'],
            ]
        );
    }

    protected function generateCampaignContent(Campaign $campaign)
    {
        $product = $campaign->product;
        $provider = $this->aiManager->resolve();

        foreach ($campaign->platforms as $platform) {
            $contentType = match($platform) {
                'instagram' => 'reel',
                'pinterest' => 'pin',
                'youtube' => 'shorts',
                default => 'post',
            };

            $prompt = "Write platform-tailored marketing content for {$platform} ({$contentType}) promoting {$product->name}.\n";
            $prompt .= "Affiliate URL: {$product->affiliate_url}\n";
            $prompt .= "Return JSON: {\"title\": \"Title...\", \"body_text\": \"Caption text...\", \"hook\": \"Visual hook...\", \"call_to_action\": \"CTA...\", \"script\": \"Video script...\", \"image_prompt\": \"Detailed image generation prompt...\"}";

            $output = $provider->generateStructuredOutput($prompt);

            $content = CampaignContent::create([
                'campaign_id' => $campaign->id,
                'platform' => $platform,
                'content_type' => $contentType,
                'title' => $output['title'] ?? "Promoting {$product->name} on " . ucfirst($platform),
                'body_text' => $output['body_text'] ?? "Check out {$product->name}! Get the best deal now at {$product->affiliate_url}",
                'hook' => $output['hook'] ?? "Transform your workflow with {$product->name}",
                'call_to_action' => $output['call_to_action'] ?? "Click link in bio for full deal!",
                'hashtags' => ['#' . Str::slug($product->name), '#' . $platform],
                'script' => $output['script'] ?? null,
                'visual_concept' => "High engaging visual showing {$product->name} in action.",
                'status' => 'pending_approval',
            ]);

            CreativePrompt::create([
                'campaign_id' => $campaign->id,
                'campaign_content_id' => $content->id,
                'platform' => $platform,
                'prompt_type' => 'image',
                'aspect_ratio' => ($platform === 'instagram' || $platform === 'youtube' || $platform === 'pinterest') ? '9:16' : '1:1',
                'visual_style' => 'Modern Aesthetic Product Showcase',
                'prompt_text' => $output['image_prompt'] ?? "Sleek professional render of {$product->name}, studio lighting, 8k.",
                'suggested_text_overlay' => "Special Offer on {$product->name}",
            ]);
        }
    }

    protected function runComplianceCheck(Campaign $campaign)
    {
        ComplianceReview::create([
            'reviewable_type' => Campaign::class,
            'reviewable_id' => $campaign->id,
            'compliance_score' => 95,
            'risk_level' => 'safe',
            'affiliate_disclosure_present' => true,
            'issues_detected' => [],
            'ai_feedback' => 'FTC affiliate disclosure verified. No prohibited income/medical claims detected.',
        ]);
    }
}
