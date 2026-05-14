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
    Schema::table('companies', function (Blueprint $table) {
        $table->string('q1_label')->default('料理');
        $table->string('q2_label')->default('接客');
        $table->string('q3_label')->default('雰囲気');
        $table->string('q4_label')->default('コスパ');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
