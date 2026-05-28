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
        // reviewsテーブルに duration_seconds が「無い」場合のみ実行する
        if (!Schema::hasColumn('reviews', 'duration_seconds')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->integer('duration_seconds')->default(0)->after('generated_text');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });
    }
};
