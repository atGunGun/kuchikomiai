<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $plans = [
            1 => [
                'name' => '無料プラン',
                'code' => 'free',
                'base_price' => 0,
                'stripe_price_id' => null,
                'max_surveys' => 1,
                'max_generations' => 3,
                'max_reviews_monthly' => 5,
            ],
            4 => [
                'name' => 'スタンダードプラン',
                'code' => 'standard',
                'base_price' => 4980,
                'stripe_price_id' => null,
                'max_surveys' => 50,
                'max_generations' => 10,
                'max_reviews_monthly' => 150,
            ],
            5 => [
                'name' => 'プレミアムプラン',
                'code' => 'premium',
                'base_price' => 9800,
                'stripe_price_id' => null,
                'max_surveys' => 20,
                'max_generations' => 20,
                'max_reviews_monthly' => null,
            ],
        ];

        foreach ($plans as $id => $values) {
            if (DB::table('plans')->where('id', $id)->exists()) {
                DB::table('plans')
                    ->where('id', $id)
                    ->update([
                        ...$values,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('plans')->insert([
                    'id' => $id,
                    ...$values,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plans')
            ->where('id', 1)
            ->update([
                'code' => null,
                'base_price' => 0,
                'max_surveys' => 3,
                'max_reviews_monthly' => null,
                'updated_at' => now(),
            ]);

        DB::table('plans')
            ->where('id', 4)
            ->update([
                'code' => null,
                'base_price' => 10000,
                'stripe_price_id' => 'price_1U8EmM0MvD48ctYoVsIYRo43',
                'max_surveys' => 10,
                'max_reviews_monthly' => null,
                'updated_at' => now(),
            ]);

        DB::table('plans')
            ->where('id', 5)
            ->update([
                'code' => null,
                'base_price' => 20000,
                'stripe_price_id' => 'price_1U8EnM0MvD48ctYoE9WBFKDz',
                'updated_at' => now(),
            ]);
    }
};