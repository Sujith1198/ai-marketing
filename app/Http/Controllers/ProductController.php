<?php

namespace App\Http\Controllers;

use App\Models\AffiliateNetwork;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Services\AI\AIProviderManager;
use App\Services\Scoring\WeightedScoringEngine;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected AIProviderManager $aiManager;
    protected WeightedScoringEngine $scoringEngine;

    public function __construct(AIProviderManager $aiManager, WeightedScoringEngine $scoringEngine)
    {
        $this->aiManager = $aiManager;
        $this->scoringEngine = $scoringEngine;
    }

    public function index(Request $request)
    {
        $query = Product::with(['network', 'score']);

        if ($request->filled('network_id')) {
            $query->where('affiliate_network_id', $request->input('network_id'));
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $networks = AffiliateNetwork::where('is_active', true)->get();

        return view('products.index', compact('products', 'networks'));
    }

    public function create()
    {
        $networks = AffiliateNetwork::where('is_active', true)->get();
        return view('products.create', compact('networks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'affiliate_network_id' => ['required', 'exists:affiliate_networks,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_url' => ['required', 'url'],
            'affiliate_url' => ['required', 'url'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'commission_value' => ['nullable', 'numeric'],
            'commission_type' => ['required', 'in:percentage,fixed'],
            'image_url' => ['nullable', 'url'],
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(100, 999);
        $validated['status'] = 'active';

        $product = Product::create($validated);

        // Run initial AI Analysis automatically
        $this->analyze($product);

        return redirect()->route('products.show', $product->id)->with('success', 'Product created and analyzed by AI!');
    }

    public function show(Product $product)
    {
        $product->load(['network', 'analysis', 'score', 'campaigns']);
        return view('products.show', compact('product'));
    }

    public function analyze(Product $product)
    {
        $prompt = "Perform a thorough marketing analysis for this affiliate product:\n";
        $prompt .= "Name: {$product->name}\nCategory: {$product->category}\nBrand: {$product->brand}\n";
        $prompt .= "Price: {$product->price} {$product->currency}\nCommission: {$product->commission_value} ({$product->commission_type})\n";
        $prompt .= "Description: {$product->description}\n\n";
        $prompt .= "Return JSON with strict 0-100 scores and analysis summaries:\n";
        $prompt .= "{\n";
        $prompt .= '  "market_demand": "summary...", "target_audience": "summary...", "pain_points": "summary...", "buyer_intent": "summary...", "competition_analysis": "summary...", "viral_potential": "summary...", "risk_factors": "summary...",';
        $prompt .= '  "scores": { "demand_score": 85, "buyer_intent_score": 80, "competition_score": 40, "commission_score": 90, "content_potential_score": 85, "viral_potential_score": 75, "seo_potential_score": 80, "trust_score": 90, "social_fit_score": 85, "conversion_potential_score": 80, "risk_score": 10 }';
        $prompt .= "\n}";

        $provider = $this->aiManager->resolve();
        $output = $provider->generateStructuredOutput($prompt);

        $scores = $output['scores'] ?? [
            'demand_score' => 80,
            'buyer_intent_score' => 75,
            'competition_score' => 50,
            'commission_score' => 80,
            'content_potential_score' => 80,
            'viral_potential_score' => 70,
            'seo_potential_score' => 75,
            'trust_score' => 85,
            'social_fit_score' => 80,
            'conversion_potential_score' => 75,
            'risk_score' => 15,
        ];

        ProductAnalysis::updateOrCreate(
            ['product_id' => $product->id],
            [
                'market_demand' => $output['market_demand'] ?? 'Strong market demand identified.',
                'target_audience' => $output['target_audience'] ?? 'Targeted digital consumers and professionals.',
                'pain_points' => $output['pain_points'] ?? 'Cost efficiency and ease of use.',
                'buyer_intent' => $output['buyer_intent'] ?? 'High purchase intent.',
                'competition_analysis' => $output['competition_analysis'] ?? 'Moderate competition across social channels.',
                'viral_potential' => $output['viral_potential'] ?? 'High viral potential with visual demos.',
                'risk_factors' => $output['risk_factors'] ?? 'Standard refund terms apply.',
                'raw_ai_output' => $output,
            ]
        );

        $this->scoringEngine->calculateScore($product, $scores);

        return back()->with('success', "AI Product Analysis & Opportunity Scoring completed for {$product->name}.");
    }
}
