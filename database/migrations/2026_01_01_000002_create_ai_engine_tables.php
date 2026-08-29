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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('driver', 50); // gemini, groq, openrouter, huggingface, custom_openai
            $table->string('api_endpoint')->nullable();
            $table->foreignId('credential_id')->nullable()->constrained('api_credentials')->onDelete('set null');
            $table->string('default_model', 100);
            $table->unsignedBigInteger('fallback_provider_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('role', 100);
            $table->text('description')->nullable();
            $table->text('system_prompt');
            $table->foreignId('ai_provider_id')->nullable()->constrained('ai_providers')->onDelete('set null');
            $table->string('model_override', 100)->nullable();
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->unsignedInteger('max_tokens')->default(2048);
            $table->integer('priority')->default(10);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_team_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('user_query');
            $table->string('status', 50)->default('completed'); // running, completed, failed
            $table->text('cmo_summary')->nullable();
            $table->json('final_recommendation')->nullable();
            $table->unsignedInteger('confidence_score')->default(0);
            $table->string('recommended_action', 100)->nullable();
            $table->string('user_decision', 50)->default('pending'); // pending, approved, rejected, follow_up
            $table->timestamps();
        });

        Schema::create('ai_team_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_team_meeting_id')->constrained('ai_team_meetings')->onDelete('cascade');
            $table->foreignId('ai_agent_id')->nullable()->constrained('ai_agents')->onDelete('set null');
            $table->string('sender_type', 20); // user, agent, system
            $table->string('agent_role', 100)->nullable();
            $table->text('content');
            $table->json('structured_payload')->nullable();
            $table->integer('execution_order')->default(1);
            $table->timestamps();
        });

        Schema::create('ai_agent_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->onDelete('cascade');
            $table->foreignId('ai_provider_id')->nullable()->constrained('ai_providers')->onDelete('set null');
            $table->string('model_used', 100)->nullable();
            $table->string('prompt_reference', 150)->nullable();
            $table->string('input_hash', 64)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 30)->default('success'); // success, failed, fallback
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->decimal('estimated_cost', 8, 6)->default(0.000000);
            $table->text('response_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->unsignedInteger('current_version')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('prompt_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_template_id')->constrained('prompt_templates')->onDelete('cascade');
            $table->unsignedInteger('version');
            $table->text('prompt_text');
            $table->string('status', 30)->default('active'); // active, archived
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_versions');
        Schema::dropIfExists('prompt_templates');
        Schema::dropIfExists('ai_agent_runs');
        Schema::dropIfExists('ai_team_messages');
        Schema::dropIfExists('ai_team_meetings');
        Schema::dropIfExists('ai_agents');
        Schema::dropIfExists('ai_providers');
    }
};
