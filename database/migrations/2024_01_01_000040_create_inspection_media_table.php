<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignUuid('result_id')->nullable()->constrained('inspection_results')->cascadeOnDelete();
            $table->foreignUuid('question_id')->nullable()->constrained('inspection_questions')->nullOnDelete();
            $table->string('type', 10)->default('image');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 50);
            $table->unsignedBigInteger('size');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['inspection_id', 'type']);
            $table->index('result_id');
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_media');
    }
};