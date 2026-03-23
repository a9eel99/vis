<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('fuel_type', 20)->nullable();
            $table->string('scoring_mode', 20)->default('scored');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('version')->default(1);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('fuel_type');
        });

        Schema::create('inspection_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('inspection_templates')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['template_id', 'sort_order']);
        });

        Schema::create('inspection_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('section_id')->constrained('inspection_sections')->cascadeOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->enum('type', ['text', 'number', 'checkbox', 'dropdown', 'photo']);
            $table->json('options')->nullable();
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->decimal('max_score', 5, 2)->default(10.00);
            $table->boolean('is_critical')->default(false);
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_questions');
        Schema::dropIfExists('inspection_sections');
        Schema::dropIfExists('inspection_templates');
    }
};