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
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 64)->unique();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('campaign_content_id')->nullable()->constrained('campaign_contents')->onDelete('set null');
            $table->string('platform', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('country', 10)->nullable();
            $table->timestamp('clicked_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_network_id')->constrained('affiliate_networks')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->foreignId('affiliate_click_id')->nullable()->constrained('affiliate_clicks')->onDelete('set null');
            $table->string('external_order_id', 150)->nullable();
            $table->decimal('conversion_value', 10, 2)->default(0.00);
            $table->decimal('commission_amount', 10, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('approved'); // pending, approved, rejected
            $table->timestamp('converted_at')->useCurrent();
            $table->string('conversion_source', 30)->default('api'); // api, webhook, csv, manual
            $table->timestamps();
        });

        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('platform', 50)->nullable();
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->decimal('revenue', 10, 2)->default(0.00);
            $table->decimal('ctr', 5, 2)->default(0.00);
            $table->decimal('conversion_rate', 5, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('optimization_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('recommendation_type', 100); // hook_refresh, time_reschedule, platform_shift, pause_low_performer
            $table->string('title');
            $table->text('details');
            $table->string('status', 30)->default('pending'); // pending, approved, rejected, applied
            $table->timestamps();
        });

        Schema::create('marketing_memory', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100); // winning_hook, best_posting_time, high_converting_type
            $table->string('key_insight');
            $table->text('insight_details');
            $table->unsignedInteger('confidence_level')->default(80);
            $table->foreignId('source_campaign_id')->nullable()->constrained('campaigns')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_memory');
        Schema::dropIfExists('optimization_recommendations');
        Schema::dropIfExists('analytics_snapshots');
        Schema::dropIfExists('conversions');
        Schema::dropIfExists('affiliate_clicks');
    }
};
