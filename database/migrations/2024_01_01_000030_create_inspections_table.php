<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number')->unique();
            $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignUuid('template_id')->constrained('inspection_templates');
            $table->foreignUuid('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 30)->nullable();
            $table->boolean('has_critical_failure')->default(false);
            $table->string('share_token', 64)->nullable()->unique();
            $table->boolean('is_hidden')->default(false);
            $table->string('hidden_reason')->nullable();
            $table->timestamp('hidden_at')->nullable();
            $table->foreignUuid('hidden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['is_hidden', 'status']);
            $table->index('inspector_id');
            $table->index('vehicle_id');
            $table->index('grade');
        });

        Schema::create('inspection_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained('inspection_questions');
            $table->text('answer')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_critical_fail')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['inspection_id', 'question_id']);
            $table->unique(['inspection_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_results');
        Schema::dropIfExists('inspections');
    }
};