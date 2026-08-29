<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AIProviderController;
use App\Http\Controllers\AITeamChatController;
use App\Http\Controllers\AITeamController;
use App\Http\Controllers\AffiliateNetworkController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClickTrackingController;
use App\Http\Controllers\CredentialVaultController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\SystemHealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Click Tracking & Server Migration Helper Routes
|--------------------------------------------------------------------------
*/
Route::get('/go/{trackingCode}', [ClickTrackingController::class, 'redirect'])->name('tracking.redirect');

Route::get('/migrate-production', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return "<div style='font-family: sans-serif; padding: 2rem;'><h1 style='color:#16a34a;'>SUCCESS: Server Database Migrated & Seeded!</h1><pre style='background:#f1f5f9; padding: 1rem; border-radius: 6px;'>{$output}</pre><br><a href='/login' style='background:#2563eb; color:white; padding:0.75rem 1.5rem; text-decoration:none; border-radius:6px;'>Go to CEO Sign In</a></div>";
    } catch (\Exception $e) {
        return "<div style='font-family: sans-serif; padding: 2rem;'><h1 style='color:#dc2626;'>Migration Error Notice:</h1><pre style='background:#fef2f2; color:#991b1b; padding: 1rem; border-radius: 6px;'>" . $e->getMessage() . "</pre></div>";
    }
});

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected SaaS Operating System Routes (CEO Admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::match(['post', 'put'], '/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // SaaS Core Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AI Team Chat & Multi-Agent Meeting Console (Specific routes before wildcard)
    Route::get('/ai-team/chat', [AITeamChatController::class, 'index'])->name('ai-team.chat');
    Route::post('/ai-team/chat', [AITeamChatController::class, 'startMeeting']);
    Route::match(['get', 'post'], '/ai-team/chat-start', [AITeamChatController::class, 'startMeeting'])->name('ai-team.chat.start');
    Route::get('/ai-team/meeting/{meeting}', [AITeamChatController::class, 'show'])->name('ai-team.chat.show');
    Route::get('/ai-team/chat/{meeting}', [AITeamChatController::class, 'show']);
    Route::match(['get', 'post'], '/ai-team/meeting/{meeting}/respond', [AITeamChatController::class, 'respond'])->name('ai-team.chat.respond');
    Route::match(['get', 'post'], '/ai-team/chat/{meeting}/respond', [AITeamChatController::class, 'respond']);

    // AI Team Agent Roster Management (Wildcard routes after specific routes)
    Route::get('/ai-team', [AITeamController::class, 'index'])->name('ai-team.index');
    Route::get('/ai-team/{agent}/edit', [AITeamController::class, 'edit'])->name('ai-team.edit');
    Route::match(['post', 'put'], '/ai-team/{agent}', [AITeamController::class, 'update'])->name('ai-team.update');
    Route::match(['get', 'post'], '/ai-team/{agent}/toggle', [AITeamController::class, 'toggleStatus'])->name('ai-team.toggle');

    // Affiliate Networks & Accounts
    Route::get('/affiliate-networks', [AffiliateNetworkController::class, 'index'])->name('affiliates.index');
    Route::get('/affiliates', [AffiliateNetworkController::class, 'index'])->name('affiliates.index.alias');
    Route::match(['post', 'put'], '/affiliate-networks/{network}', [AffiliateNetworkController::class, 'update'])->name('affiliates.update');
    
    Route::get('/affiliate-accounts', [\App\Http\Controllers\AffiliateAccountController::class, 'index'])->name('affiliate-accounts.index');
    Route::post('/affiliate-accounts', [\App\Http\Controllers\AffiliateAccountController::class, 'store'])->name('affiliate-accounts.store');
    Route::post('/affiliate-accounts/{account}/test', [\App\Http\Controllers\AffiliateAccountController::class, 'testConnection'])->name('affiliate-accounts.test');
    Route::delete('/affiliate-accounts/{account}', [\App\Http\Controllers\AffiliateAccountController::class, 'destroy'])->name('affiliate-accounts.destroy');

    // Product Opportunity Center & Watchlist
    Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
    Route::get('/watchlist', [\App\Http\Controllers\WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/{product}/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggle'])->name('watchlist.toggle');

    // Product Management & Import
    Route::get('/products/import', [\App\Http\Controllers\ProductImportController::class, 'showImportForm'])->name('products.import');
    Route::post('/products/import', [\App\Http\Controllers\ProductImportController::class, 'processImport'])->name('products.import.process');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Product AI Analysis & AI Review Workflows
    Route::match(['get', 'post'], '/products/{product}/analyze', [\App\Http\Controllers\ProductAnalysisController::class, 'analyze'])->name('products.analyze');
    Route::get('/products/{product}/analysis-history', [\App\Http\Controllers\ProductAnalysisController::class, 'history'])->name('products.analysis-history');
    Route::get('/products/{product}/ask-ai-team', [\App\Http\Controllers\ProductAIReviewController::class, 'askAiTeam'])->name('products.ask-ai-team');

    // Settings Scoring Weights
    Route::post('/settings/scoring', [SettingsController::class, 'updateScoringWeights'])->name('settings.scoring.update');

    // Campaign Manager & Creation Wizard
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/wizard', [CampaignController::class, 'wizard'])->name('campaigns.wizard');
    Route::post('/campaigns/wizard', [CampaignController::class, 'storeWizard'])->name('campaigns.wizard.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');

    // Central Human Approval Center
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::match(['get', 'post'], '/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::match(['get', 'post'], '/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    Route::match(['get', 'post'], '/approvals/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('approvals.bulk-approve');

    // Content Calendar & Scheduler
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Social Account Management
    Route::get('/social-accounts', [SocialAccountController::class, 'index'])->name('social-accounts.index');
    Route::get('/social-accounts/{platform}/connect', [SocialAccountController::class, 'connect'])->name('social-accounts.connect');
    Route::match(['get', 'post'], '/social-accounts/{account}/disconnect', [SocialAccountController::class, 'disconnect'])->name('social-accounts.disconnect');

    // Analytics Dashboard
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // AI Provider Manager & Fallback Chain
    Route::get('/providers', [AIProviderController::class, 'index'])->name('providers.index');
    Route::match(['post', 'put'], '/providers/{provider}', [AIProviderController::class, 'update'])->name('providers.update');
    Route::match(['get', 'post'], '/providers/{provider}/test', [AIProviderController::class, 'testConnection'])->name('providers.test');

    // Secure API Credential Vault (AES-256-GCM)
    Route::get('/vault', [CredentialVaultController::class, 'index'])->name('vault.index');
    Route::post('/vault', [CredentialVaultController::class, 'store'])->name('vault.store');
    Route::match(['post', 'put'], '/vault/{credential}/replace', [CredentialVaultController::class, 'replace'])->name('vault.replace');
    Route::match(['get', 'post', 'delete'], '/vault/{credential}', [CredentialVaultController::class, 'destroy'])->name('vault.destroy');

    // Audit Trail Logs & System Health Diagnostics
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/system/health', [SystemHealthController::class, 'index'])->name('system.health');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
