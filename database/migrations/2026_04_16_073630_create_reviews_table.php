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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // ↓この2行を追加します
            $table->text('prompt_details')->nullable(); // 入力した感想（料理や接客など）の記録
            $table->text('generated_text');             // AIが作ってくれた口コミ文章
            
            $table->timestamps(); // 作成日時・更新日時（自動）
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
