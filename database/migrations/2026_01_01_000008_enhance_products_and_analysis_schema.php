<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('products', 'affiliate_account_id')) {
                $table->foreignId('affiliate_account_id')->nullable()->after('affiliate_network_id')->constrained('affiliate_accounts')->onDelete('set null');
            }
            if (!Schema::hasColumn('products', 'subcategory')) {
                $table->string('subcategory', 100)->nullable()->after('category');
            }
            if (!Schema::hasColumn('products', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('metadata');
            }
        });

        Schema::table('product_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('product_analyses', 'analysis_version')) {
                $table->unsignedInteger('analysis_version')->default(1)->after('product_id');
            }
            if (!Schema::hasColumn('product_analyses', 'provider')) {
                $table->string('provider', 50)->nullable()->after('analysis_version');
            }
            if (!Schema::hasColumn('product_analyses', 'model')) {
                $table->string('model', 50)->nullable()->after('provider');
            }
            if (!Schema::hasColumn('product_analyses', 'status')) {
                $table->string('status', 30)->default('completed')->after('model');
            }
            if (!Schema::hasColumn('product_analyses', 'confidence_score')) {
                $table->unsignedInteger('confidence_score')->default(85)->after('status');
            }
        });

        Schema::table('product_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('product_scores', 'product_analysis_id')) {
                $table->foreignId('product_analysis_id')->nullable()->after('product_id')->constrained('product_analyses')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        // Revert schema changes if needed
    }
};
