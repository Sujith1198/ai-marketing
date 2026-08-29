<?php

namespace App\Services\Product;

use App\Models\AIAgent;
use App\Models\Product;
use App\Services\AI\AIProviderManager;

class ProductAIReviewService
{
    protected AIProviderManager $providerManager;

    public function __construct(AIProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    /**
     * Conduct an AI Team review specifically for a product.
     */
    public function reviewProduct(Product $product): array
    {
        $agents = AIAgent::where('is_enabled', true)
            ->whereIn('slug', ['market-research-agent', 'seo-specialist-agent', 'social-media-strategist-agent', 'compliance-officer-agent', 'cmo-agent'])
            ->get();

        $reviews = [];
        $strengths = ['High Commission Rate', 'Strong Conversion History', 'Clear Market Demand'];
        $weaknesses = ['Moderate Ad Competition', 'Requires Quality Content Creation'];

        foreach ($agents as $agent) {
            $provider = $this->providerManager->resolve($agent->provider);
            $prompt = "Product Name: {$product->name}\nCategory: {$product->category}\nPrice: {$product->price}\nCommission: {$product->commission_value}\n\nRole: {$agent->system_prompt}\nProvide specialized evaluation feedback.";

            $opinion = $provider->generateText($prompt);
            $reviews[] = [
                'agent_name' => $agent->name,
                'role' => $agent->role,
                'opinion' => $opinion,
            ];
        }

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'agent_reviews' => $reviews,
            'main_strengths' => $strengths,
            'main_weaknesses' => $weaknesses,
            'best_audience' => 'Tech-savvy Founders & Digital Marketers',
            'best_content_angle' => 'Software Comparison & ROI Case Study',
            'best_platforms' => ['YouTube Shorts', 'Instagram Reels', 'SEO Blog'],
            'risk_level' => 'Low',
            'cmo_decision' => 'Approved for Campaign Creation',
        ];
    }
}
