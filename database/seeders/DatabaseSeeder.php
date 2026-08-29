<?php

namespace Database\Seeders;

use App\Enums\CampaignStatus;
use App\Enums\RecommendationLevel;

use App\Models\AIAgent;
use App\Models\AIProvider;
use App\Models\AffiliateNetwork;
use App\Models\Campaign;
use App\Models\CampaignContent;
use App\Models\CampaignStrategy;
use App\Models\CreativePrompt;
use App\Models\MarketingMemory;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Models\ProductScore;
use App\Models\SocialPlatform;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default CEO User
        $user = User::updateOrCreate(
            ['email' => 'ceo@aimarketing.test'],
            [
                'name' => 'CEO Admin',
                'password' => Hash::make('password'),
                'role' => 'ceo',
                'is_active' => true,
            ]
        );

        // 2. System Settings
        SystemSetting::set('app_name', 'AI Marketing Team', 'general');
        SystemSetting::set('require_human_approval', 'true', 'security');
        SystemSetting::set('default_currency', 'USD', 'general');
        SystemSetting::set('default_timezone', 'UTC', 'general');
        SystemSetting::set('default_disclosure', 'Disclosure: This post contains affiliate links. We may earn a commission if you make a purchase through these links at no extra cost to you.', 'compliance');

        // 3. AI Providers
        $gemini = AIProvider::updateOrCreate(
            ['slug' => 'google-gemini'],
            [
                'name' => 'Google Gemini',
                'driver' => 'gemini',
                'default_model' => 'gemini-1.5-flash',
                'is_active' => true,
                'is_primary' => true,
            ]
        );

        $groq = AIProvider::updateOrCreate(
            ['slug' => 'groq'],
            [
                'name' => 'Groq (Ultra-Fast Llama)',
                'driver' => 'groq',
                'default_model' => 'llama-3.3-70b-versatile',
                'is_active' => true,
                'is_primary' => false,
            ]
        );

        $openrouter = AIProvider::updateOrCreate(
            ['slug' => 'openrouter'],
            [
                'name' => 'OpenRouter Multi-Model',
                'driver' => 'openrouter',
                'default_model' => 'meta-llama/llama-3.1-70b-instruct:free',
                'is_active' => true,
                'is_primary' => false,
            ]
        );

        // Set Gemini fallback to Groq
        $gemini->update(['fallback_provider_id' => $groq->id]);

        // 4. Core AI Marketing Agents
        $agentsData = [
            [
                'name' => 'Chief Marketing Officer Agent',
                'slug' => 'cmo-agent',
                'role' => 'Chief Marketing Officer',
                'description' => 'Synthesizes all marketing agent findings into actionable strategic recommendations.',
                'system_prompt' => 'You are the Chief Marketing Officer (CMO). You lead the AI marketing team. Analyze inputs from Product Hunter, Copywriter, SEO, and Compliance agents to deliver high-converting strategic directives.',
                'priority' => 1,
            ],
            [
                'name' => 'Product Hunter Agent',
                'slug' => 'product-hunter-agent',
                'role' => 'Product Hunter',
                'description' => 'Discovers high-demand, high-margin affiliate products across networks.',
                'system_prompt' => 'You are a master Product Hunter. Evaluate product demand, market viability, commission structures, and buyer intent.',
                'priority' => 2,
            ],
            [
                'name' => 'Market Research Agent',
                'slug' => 'market-research-agent',
                'role' => 'Market Research Analyst',
                'description' => 'Analyzes customer demographics, pain points, and competitor positioning.',
                'system_prompt' => 'You are a Market Research Specialist. Map target audience segments, emotional pain points, and unique selling propositions.',
                'priority' => 3,
            ],
            [
                'name' => 'Copywriter Agent',
                'slug' => 'copywriter-agent',
                'role' => 'Direct Response Copywriter',
                'description' => 'Crafts high-converting social media scripts, hooks, captions, and call-to-actions.',
                'system_prompt' => 'You are a world-class Direct Response Copywriter. Create irresistible hooks, compelling problem-solution narratives, and subtle CTAs.',
                'priority' => 4,
            ],
            [
                'name' => 'SEO Specialist Agent',
                'slug' => 'seo-specialist-agent',
                'role' => 'SEO & Keyword Specialist',
                'description' => 'Identifies low-competition, high-intent search keywords and Pinterest tags.',
                'system_prompt' => 'You are an SEO & Pinterest Keyword Strategist. Discover high-converting search terms, hashtags, and pin titles.',
                'priority' => 5,
            ],
            [
                'name' => 'Compliance Agent',
                'slug' => 'compliance-agent',
                'role' => 'Affiliate Compliance Officer',
                'description' => 'Ensures strict compliance with FTC disclosures, trademark guidelines, and ad policies.',
                'system_prompt' => 'You are the Compliance Officer. Inspect marketing material for FTC affiliate disclosures, misleading health/financial claims, and platform policy violations.',
                'priority' => 6,
            ],
            [
                'name' => 'Social Media Strategist Agent',
                'slug' => 'social-media-strategist-agent',
                'role' => 'Social Media Director',
                'description' => 'Optimizes posting schedules, visual formats, and short-form video hooks.',
                'system_prompt' => 'You are the Social Media Strategist. Tailor content styles specifically for Instagram Reels, Facebook, Pinterest Pins, and YouTube Shorts.',
                'priority' => 7,
            ],
        ];

        foreach ($agentsData as $aData) {
            AIAgent::updateOrCreate(
                ['slug' => $aData['slug']],
                array_merge($aData, [
                    'ai_provider_id' => $gemini->id,
                    'temperature' => 0.70,
                    'max_tokens' => 2048,
                    'is_enabled' => true,
                ])
            );
        }

        // 5. Affiliate Networks
        $amazon = AffiliateNetwork::updateOrCreate(
            ['slug' => 'amazon-associates'],
            [
                'name' => 'Amazon Associates',
                'driver' => 'amazon',
                'tracking_id' => 'aimarketing-20',
                'affiliate_username' => 'amazon_affiliate_id',
                'portal_url' => 'https://affiliate-program.amazon.com',
                'capabilities' => ['product_search', 'product_details', 'affiliate_link_generation', 'manual_import'],
                'is_active' => true,
            ]
        );

        $digistore = AffiliateNetwork::updateOrCreate(
            ['slug' => 'digistore24'],
            [
                'name' => 'Digistore24',
                'driver' => 'digistore24',
                'tracking_id' => 'aimarketing',
                'affiliate_username' => 'digistore_affiliate_user',
                'portal_url' => 'https://www.digistore24.com',
                'capabilities' => ['product_search', 'commission_data', 'conversion_tracking', 'manual_import'],
                'is_active' => true,
            ]
        );

        $hostinger = AffiliateNetwork::updateOrCreate(
            ['slug' => 'hostinger'],
            [
                'name' => 'Hostinger Affiliate Program',
                'driver' => 'hostinger',
                'tracking_id' => 'hostinger_ai',
                'affiliate_username' => 'hostinger_partner_user',
                'portal_url' => 'https://hpanel.hostinger.com/affiliate',
                'capabilities' => ['manual_product', 'manual_affiliate_link', 'manual_conversion'],
                'is_active' => true,
            ]
        );

        // 6. Social Platforms
        $platforms = [
            ['name' => 'Instagram', 'slug' => 'instagram'],
            ['name' => 'Facebook', 'slug' => 'facebook'],
            ['name' => 'Pinterest', 'slug' => 'pinterest'],
            ['name' => 'YouTube', 'slug' => 'youtube'],
        ];
        foreach ($platforms as $p) {
            SocialPlatform::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['oauth_supported' => true, 'is_active' => true]));
        }

        // 7. Demo Product
        $product = Product::updateOrCreate(
            ['slug' => 'hostinger-premium-web-hosting'],
            [
                'affiliate_network_id' => $hostinger->id,
                'external_product_id' => 'HST-PREM-01',
                'name' => 'Hostinger Premium Web Hosting',
                'category' => 'Web Hosting & SaaS',
                'brand' => 'Hostinger',
                'description' => 'Fast, secure, and affordable WordPress web hosting with free domain, SSL, and AI website builder.',
                'product_url' => 'https://www.hostinger.com/web-hosting',
                'affiliate_url' => 'https://www.hostinger.com/web-hosting?referral=aimarketing',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&q=80',
                'price' => 2.99,
                'currency' => 'USD',
                'commission_type' => 'percentage',
                'commission_value' => 60.00,
                'commission_notes' => '60% baseline commission per customer subscription.',
                'status' => 'active',
                'source' => 'manual',
            ]
        );

        ProductAnalysis::updateOrCreate(
            ['product_id' => $product->id],
            [
                'market_demand' => 'High global search volume for affordable WordPress hosting & website builders.',
                'target_audience' => 'Freelancers, small business owners, affiliate marketers, and web developers.',
                'pain_points' => 'High hosting costs at SiteGround/Bluehost, complicated setup, slow site load times.',
                'buyer_intent' => 'High intent buyers looking for renewal discounts and free domain bundle deals.',
                'problem_solved' => 'Provides lightning-fast LiteSpeed web hosting at 80% lower cost.',
                'emotional_triggers' => 'Frustration with expensive hosting renewals, desire to start online business easily.',
                'competition_analysis' => 'Moderate competition on YouTube & Blogs; high potential on Instagram Reels & Pinterest.',
                'content_potential' => 'Excellent for quick video tutorials, cost comparison infographics, and speed tests.',
                'viral_potential' => 'High viral potential around "How to build a website in 10 minutes with AI".',
                'seo_opportunity' => 'Low keyword difficulty for niche long-tail terms like "cheapest hosting with free SSL 2026".',
                'social_media_fit' => 'Ideal for Pinterest infographics and YouTube Shorts speed benchmarks.',
                'risk_factors' => 'Standard refund window policies apply.',
                'compliance_concerns' => 'Must disclose affiliate relationship clearly in captions.',
            ]
        );

        ProductScore::updateOrCreate(
            ['product_id' => $product->id],
            [
                'demand_score' => 90,
                'buyer_intent_score' => 88,
                'competition_score' => 45,
                'commission_score' => 92,
                'content_potential_score' => 88,
                'viral_potential_score' => 82,
                'seo_potential_score' => 85,
                'trust_score' => 94,
                'social_fit_score' => 90,
                'conversion_potential_score' => 87,
                'risk_score' => 10,
                'overall_opportunity_score' => 88,
                'recommendation' => RecommendationLevel::STRONG_PROMOTE->value,
                'score_breakdown' => [
                    'weighted_base' => 88.5,
                    'penalties' => 0,
                ],
            ]
        );

        // 8. Demo Campaign
        $campaign = Campaign::updateOrCreate(
            ['slug' => 'hostinger-ai-website-builder-launch'],
            [
                'product_id' => $product->id,
                'affiliate_network_id' => $hostinger->id,
                'name' => 'Hostinger AI Website Builder Launch',
                'goal' => 'Drive 100 new hosting signups via short-form video & Pinterest Pins.',
                'target_audience' => 'Aspiring entrepreneurs, freelancers, bloggers.',
                'marketing_angle' => 'Stop paying $50/mo for web hosting. Build a site in 5 mins with AI for $2.99/mo.',
                'platforms' => ['instagram', 'pinterest', 'youtube'],
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'budget' => 0.00,
                'status' => CampaignStatus::PENDING_APPROVAL->value,
                'ai_strategy_summary' => 'Focus on short-form screen recordings showing site generation in under 60 seconds.',
            ]
        );

        CampaignStrategy::updateOrCreate(
            ['campaign_id' => $campaign->id],
            [
                'customer_persona' => ['age' => '22-45', 'interests' => ['Side Hustles', 'Web Design', 'WordPress', 'AI Tools']],
                'awareness_stage' => 'Problem Aware',
                'content_pillars' => ['AI Tools Demo', 'Hosting Cost Comparison', 'Website Speed Benchmark'],
                'primary_hooks' => [
                    'I built a full business website in 3 minutes using AI (and it cost $2.99)',
                    'Stop paying Webflow $29/mo when Hostinger gives you AI + Hosting for $2.99',
                ],
                'cta_strategy' => ['Use link in bio to claim 80% discount + free domain.'],
                'hashtags' => ['#webdesign', '#sidehustle', '#wordpress', '#hostinger', '#aitools'],
            ]
        );

        $content = CampaignContent::updateOrCreate(
            [
                'campaign_id' => $campaign->id,
                'platform' => 'instagram',
                'content_type' => 'reel',
            ],
            [
                'title' => 'Build a Website with AI in 60 Seconds',
                'body_text' => "Did you know you can build a complete WordPress website in under 60 seconds with AI?\n\nHostinger includes a free AI website builder, free domain, and SSL for just \$2.99/month.\n\nComment 'HOST' or click the link in bio to grab the 80% off discount!",
                'hook' => 'Stop paying web designers $1,000 when AI does it in 60 seconds.',
                'call_to_action' => 'Click link in bio to get 80% OFF + free domain!',
                'hashtags' => ['#webdesign', '#aitools', '#hostinger', '#sidehustle2026'],
                'script' => "Visual: Screen recording of Hostinger AI builder typing prompt 'Coffee shop website'.\nVoiceover: Watch AI build this entire site in 15 seconds. Domain included!",
                'visual_concept' => 'Side-by-side video comparing manual coding vs AI website generator.',
                'status' => 'pending_approval',
            ]
        );

        CreativePrompt::updateOrCreate(
            ['campaign_id' => $campaign->id],
            [
                'campaign_content_id' => $content->id,
                'platform' => 'instagram',
                'prompt_type' => 'image',
                'aspect_ratio' => '9:16',
                'visual_style' => 'Modern SaaS UI Mockup',
                'prompt_text' => 'High tech workspace laptop screen displaying a sleek dark-mode AI website generator UI, vibrant purple and blue glow, futuristic desktop setup, 8k render.',
                'suggested_text_overlay' => 'Build Websites 10x Faster with AI ($2.99/mo)',
                'recommended_tool' => 'Flux.1 / Midjourney v6',
            ]
        );

        // 9. Marketing Memory
        MarketingMemory::updateOrCreate(
            ['key_insight' => 'Short-form screen recordings of AI website building generate 3x higher CTR than static graphics.'],
            [
                'category' => 'winning_hook',
                'insight_details' => 'Audience responds strongly to real-time timer overlays showing website creation speed.',
                'confidence_level' => 92,
                'source_campaign_id' => $campaign->id,
            ]
        );
    }
}
