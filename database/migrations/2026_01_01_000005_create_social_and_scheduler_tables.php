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
        Schema::create('social_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->boolean('oauth_supported')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_platform_id')->constrained('social_platforms')->onDelete('cascade');
            $table->string('account_name', 150);
            $table->string('account_id', 150)->nullable();
            $table->foreignId('credential_id')->nullable()->constrained('api_credentials')->onDelete('set null');
            $table->timestamp('token_expires_at')->nullable();
            $table->string('status', 30)->default('connected'); // connected, expired, disconnected, error
            $table->json('permissions')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('campaign_content_id')->constrained('campaign_contents')->onDelete('cascade');
            $table->foreignId('social_account_id')->nullable()->constrained('social_accounts')->onDelete('set null');
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->onDelete('set null');
            $table->string('platform', 50);
            $table->timestamp('scheduled_at');
            $table->string('timezone', 50)->default('UTC');
            $table->string('status', 30)->default('scheduled'); // draft, pending_approval, approved, scheduled, publishing, published, failed, cancelled
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('external_post_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('published_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_post_id')->constrained('scheduled_posts')->onDelete('cascade');
            $table->string('platform', 50);
            $table->string('external_post_id');
            $table->string('post_url', 500)->nullable();
            $table->timestamp('published_at');
            $table->json('metrics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('published_posts');
        Schema::dropIfExists('scheduled_posts');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('social_platforms');
    }
};
