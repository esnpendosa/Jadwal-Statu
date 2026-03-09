<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Risk Rules
        Schema::create('risk_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('late_return, quantity_mismatch, damage, etc');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->string('category', 30)->default('behavior')->comment('behavior, damage, loss');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Risk Scores (per PIC/project)
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('borrow_transaction_id')->nullable()->constrained('borrow_transactions')->nullOnDelete();
            $table->foreignId('return_transaction_id')->nullable()->constrained('return_transactions')->nullOnDelete();
            $table->foreignId('risk_rule_id')->constrained('risk_rules');
            $table->unsignedInteger('points_added');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('project_id');
        });

        // AI Suggestion Rules
        Schema::create('ai_suggestion_rules', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_type', 50)->comment('late_return_frequency, damage_frequency, mismatch_frequency, high_risk_score');
            $table->unsignedInteger('threshold')->comment('Trigger when count >= threshold');
            $table->string('period_days', 10)->default('30')->comment('Time window in days');
            $table->string('target', 30)->default('pic')->comment('pic, project, inventory, location');
            $table->json('suggestion')->comment('JSON with translations: {id: "", en: "", zh: ""}');
            $table->string('severity', 20)->default('warning')->comment('info, warning, critical');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // AI Suggestions (generated/applied)
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_suggestion_rule_id')->constrained('ai_suggestion_rules');
            $table->string('target_type', 30)->comment('user, project, inventory, location');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable();
            $table->json('suggestion_text')->comment('Translated text');
            $table->string('severity', 20)->default('warning');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('generated_at');
            $table->timestamps();
            
            $table->index(['target_type', 'target_id']);
            $table->index(['is_read', 'is_dismissed']);
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });

        // Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->comment('create, update, delete, login, logout, etc');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });

        // System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string')->comment('string, boolean, integer, json, text');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index('group');
        });

        // Email Templates
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('reminder_h1, overdue, risk_high, etc');
            $table->json('subject')->comment('Translated subjects');
            $table->json('body')->comment('Translated HTML bodies');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('ai_suggestions');
        Schema::dropIfExists('ai_suggestion_rules');
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('risk_rules');
    }
};
