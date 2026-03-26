<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_results', function (Blueprint $table) {
            $table->unsignedTinyInteger('remarks_score')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('inspection_results', function (Blueprint $table) {
            $table->dropColumn('remarks_score');
        });
    }
};