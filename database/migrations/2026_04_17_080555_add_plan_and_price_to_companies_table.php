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
            // プランID（外部キー制約なしでシンプルに追加）
            $table->unsignedBigInteger('plan_id')->nullable()->after('agency_id');
            // 実際の請求金額（代理店が定価から変更できるようにする）
            $table->integer('applied_price')->nullable()->after('plan_id');
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
