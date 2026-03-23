<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('vin', 17)->unique()->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->string('fuel_type', 20)->nullable();
            $table->string('transmission', 20)->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_phone', 20)->nullable();
            $table->string('owner_email')->nullable();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['make', 'model', 'year']);
            $table->index('license_plate');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};