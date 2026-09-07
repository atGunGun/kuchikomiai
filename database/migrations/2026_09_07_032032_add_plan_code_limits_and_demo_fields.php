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
            $table->string('code')->nullable()->unique()->after('name');
            $table->integer('max_reviews_monthly')->nullable()->after('max_generations');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('demo_plan_id')->nullable()->after('plan_id');
            $table->timestamp('demo_expires_at')->nullable()->after('demo_plan_id');

            $table->foreign('demo_plan_id')
                ->references('id')
                ->on('plans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['demo_plan_id']);
            $table->dropColumn([
                'demo_plan_id',
                'demo_expires_at',
            ]);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'max_reviews_monthly',
            ]);
        });
    }
};