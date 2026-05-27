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
            // htmlタグをそのまま保存できるように text型 で追加
            $table->text('welcome_message')->nullable()->after('logo_path');
            $table->text('completion_message')->nullable()->after('welcome_message');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['welcome_message', 'completion_message']);
        });
    }
};
