<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // カラムが存在しない場合に追加する
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'needs_password_change')) {
                $table->boolean('needs_password_change')->default(false)->after('is_admin');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('company')->after('needs_password_change');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'needs_password_change', 'role']);
        });
    }
};