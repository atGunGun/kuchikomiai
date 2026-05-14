<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('companies', function (Blueprint $table) {
    //         $table->id();
    //         $table->timestamps();
    //     });
    // }
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            // ↓ここを追加：どの代理店に所属しているか（代理店のUser ID）
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');
            // ↓ここを追加：どのユーザー（企業アカウント）がログインするか
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('name');             // 企業名・店舗名
            $table->string('address')->nullable(); // 住所
            $table->string('google_map_url')->nullable(); // GoogleMAPリンク
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
