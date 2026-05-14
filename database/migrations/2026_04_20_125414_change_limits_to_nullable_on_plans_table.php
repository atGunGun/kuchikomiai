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
        Schema::table('plans', function (Blueprint $table) {
            // nullable() をつけることで、空欄（NULL＝無制限）を許容します
            $table->integer('max_surveys')->nullable()->change();
            $table->integer('max_generations')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nullable_on_plans', function (Blueprint $table) {
            //
        });
    }
};
