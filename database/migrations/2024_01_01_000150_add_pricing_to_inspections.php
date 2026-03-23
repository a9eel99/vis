<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Price per inspection template
        Schema::table('inspection_templates', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0)->after('scoring_mode')
                  ->comment('Price in JOD for this inspection type');
        });

        // Payment tracking on inspections
        Schema::table('inspections', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0)->after('notes')
                  ->comment('Price charged for this inspection');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('price');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
            $table->decimal('discount', 8, 2)->default(0)->after('paid_at');

            $table->index('payment_status');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['paid_at']);
            $table->dropColumn(['price', 'payment_status', 'paid_at', 'discount']);
        });

        Schema::table('inspection_templates', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};