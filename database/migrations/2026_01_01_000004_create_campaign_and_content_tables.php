<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('affiliate_network_id')->constrained('affiliate_networks')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('goal', 150)->nullable();
            $table->text('target_audience')->nullable();
            $table->text('marketing_angle')->nullable();
            $table->json('platforms'); // ["instagram", "facebook", "pinterest", "youtube"]
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 10, 2)->default(0.00);
            $table->string('status', 30)->default('draft'); // draft, ai_reviewing, pending_approval, approved, scheduled, active, paused, completed, rejected, archived
            $table->text('ai_strategy_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('campaign_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->json('customer_persona')->nullable();
            $table->json('emotional_motivations')->nullable();
            $table->string('awareness_stage', 100)->nullable();
            $table->json('content_pillars')->nullable();
            $table->json('primary_hooks')->nullable();
            $table->json('secondary_hooks')->nullable();
            $table->json('cta_strategy')->nullable();
            $table->json('platform_strategy')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->json('hashtags')->nullable();
            $table->json('objections_handling')->nullable();
            $table->timestamps();
        });

        Schema::create('campaign_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('platform', 50); // instagram, facebook, pinterest, youtube
            $table->string('content_type', 50); // reel, post, pin, shorts, carousel
            $table->string('title')->nullable();
            $table->text('body_text');
            $table->text('hook')->nullable();
            $table->text('call_to_action')->nullable();
            $table->json('hashtags')->nullable();
            $table->text('script')->nullable();
            $table->text('visual_concept')->nullable();
            $table->string('status', 30)->default('pending_approval'); // draft, pending_approval, approved, scheduled, published, rejected
            $table->timestamps();
        });

        Schema::create('creative_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('campaign_content_id')->nullable()->constrained('campaign_contents')->onDelete('set null');
            $table->string('platform', 50);
            $table->string('prompt_type', 50)->default('image'); // image, video
            $table->string('aspect_ratio', 10)->default('9:16'); // 1:1, 4:5, 9:16, 16:9, 2:3
            $table->string('visual_style', 100)->nullable();
            $table->text('prompt_text');
            $table->string('suggested_text_overlay')->nullable();
            $table->text('negative_prompt')->nullable();
            $table->string('recommended_tool', 100)->default('Midjourney / Flux / DALL-E');
            $table->timestamps();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->string('media_type', 30)->default('image'); // image, video
            $table->string('platform', 50)->nullable();
            $table->string('aspect_ratio', 10)->nullable();
            $table->string('source', 30)->default('manual'); // manual, ai_generated
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviewable_type', 100);
            $table->unsignedBigInteger('reviewable_id');
            $table->unsignedInteger('compliance_score')->default(100);
            $table->string('risk_level', 30)->default('safe'); // safe, review_recommended, high_risk, blocked
            $table->boolean('affiliate_disclosure_present')->default(true);
            $table->json('issues_detected')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type', 100);
            $table->unsignedBigInteger('approvable_id');
            $table->string('approval_type', 50); // campaign, content, post, recommendation
            $table->string('status', 30)->default('pending'); // pending, approved, rejected, revised
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->unsignedInteger('ai_confidence')->default(0);
            $table->string('risk_level', 30)->default('safe');
            $table->timestamps();

            $table->index(['approvable_type', 'approvable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('compliance_reviews');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('creative_prompts');
        Schema::dropIfExists('campaign_contents');
        Schema::dropIfExists('campaign_strategies');
        Schema::dropIfExists('campaigns');
    }
};
