<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // カラムがまだ存在しない場合のみ追加する
            if (!Schema::hasColumn('companies', 'selected_survey_id')) {
                // SQLiteでエラーになりにくい、シンプルなID保存枠として作成
                $table->unsignedBigInteger('selected_survey_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'selected_survey_id')) {
                $table->dropColumn('selected_survey_id');
            }
        });
    }
};