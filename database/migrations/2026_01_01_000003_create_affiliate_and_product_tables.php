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
        Schema::create('affiliate_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('driver', 50); // amazon, digistore24, hostinger, custom
            $table->string('tracking_id', 150)->nullable();
            $table->string('affiliate_username', 150)->nullable();
            $table->string('portal_url', 500)->nullable();
            $table->foreignId('credential_id')->nullable()->constrained('api_credentials')->onDelete('set null');
            $table->json('capabilities'); // ["product_search", "conversion_tracking", etc]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_network_id')->constrained('affiliate_networks')->onDelete('cascade');
            $table->string('external_product_id', 150)->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('product_url', 500);
            $table->string('affiliate_url', 500);
            $table->string('image_url', 500)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('commission_type', 30)->default('percentage'); // percentage, fixed
            $table->decimal('commission_value', 10, 2)->default(0.00);
            $table->text('commission_notes')->nullable();
            $table->string('status', 30)->default('draft'); // draft, active, archived
            $table->string('source', 30)->default('manual'); // manual, csv, api
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('product_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->text('market_demand')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('pain_points')->nullable();
            $table->text('buyer_intent')->nullable();
            $table->text('problem_solved')->nullable();
            $table->text('emotional_triggers')->nullable();
            $table->text('competition_analysis')->nullable();
            $table->text('product_differentiation')->nullable();
            $table->text('pricing_attractiveness')->nullable();
            $table->text('commission_attractiveness')->nullable();
            $table->text('content_potential')->nullable();
            $table->text('viral_potential')->nullable();
            $table->text('seo_opportunity')->nullable();
            $table->text('social_media_fit')->nullable();
            $table->text('risk_factors')->nullable();
            $table->text('compliance_concerns')->nullable();
            $table->json('raw_ai_output')->nullable();
            $table->timestamps();
        });

        Schema::create('product_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('demand_score')->default(0);
            $table->unsignedInteger('buyer_intent_score')->default(0);
            $table->unsignedInteger('competition_score')->default(0);
            $table->unsignedInteger('commission_score')->default(0);
            $table->unsignedInteger('content_potential_score')->default(0);
            $table->unsignedInteger('viral_potential_score')->default(0);
            $table->unsignedInteger('seo_potential_score')->default(0);
            $table->unsignedInteger('trust_score')->default(0);
            $table->unsignedInteger('social_fit_score')->default(0);
            $table->unsignedInteger('conversion_potential_score')->default(0);
            $table->unsignedInteger('risk_score')->default(0);
            $table->unsignedInteger('overall_opportunity_score')->default(0);
            $table->string('recommendation', 50)->default('TEST'); // STRONG_PROMOTE, PROMOTE, TEST, WATCH, AVOID
            $table->json('score_breakdown')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_scores');
        Schema::dropIfExists('product_analyses');
        Schema::dropIfExists('products');
        Schema::dropIfExists('affiliate_networks');
    }
};
