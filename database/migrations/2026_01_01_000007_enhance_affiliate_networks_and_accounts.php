<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_networks', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliate_networks', 'status')) {
                $table->string('status', 30)->default('active')->after('driver');
            }
            if (!Schema::hasColumn('affiliate_networks', 'supports_api')) {
                $table->boolean('supports_api')->default(true)->after('status');
            }
            if (!Schema::hasColumn('affiliate_networks', 'supports_manual_import')) {
                $table->boolean('supports_manual_import')->default(true)->after('supports_api');
            }
            if (!Schema::hasColumn('affiliate_networks', 'website_url')) {
                $table->string('website_url', 500)->nullable()->after('supports_manual_import');
            }
            if (!Schema::hasColumn('affiliate_networks', 'logo')) {
                $table->string('logo', 500)->nullable()->after('website_url');
            }
            if (!Schema::hasColumn('affiliate_networks', 'description')) {
                $table->text('description')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('affiliate_networks', 'settings')) {
                $table->json('settings')->nullable()->after('capabilities');
            }
        });

        if (!Schema::hasTable('affiliate_accounts')) {
            Schema::create('affiliate_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('affiliate_network_id')->constrained('affiliate_networks')->onDelete('cascade');
                $table->string('name', 150);
                $table->string('tracking_id', 150)->nullable();
                $table->string('status', 30)->default('connected'); // connected, manual, needs_attention, disabled
                $table->foreignId('credential_id')->nullable()->constrained('api_credentials')->onDelete('set null');
                $table->json('settings')->nullable();
                $table->timestamp('last_tested_at')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_accounts');
    }
};
