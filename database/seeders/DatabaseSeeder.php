<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ① 運営
        User::create(['name' => '運営管理者', 'email' => 'admin@test.com', 'password' => Hash::make('password'), 'role' => 'admin']);

        // ② 代理店A
        $agency = User::create(['name' => 'テスト代理店A', 'email' => 'agency@test.com', 'password' => Hash::make('password'), 'role' => 'agency']);

        // ③ 企業（店舗）アカウント
        $companyUser = User::create(['name' => '焼肉 テスト店', 'email' => 'company@test.com', 'password' => Hash::make('password'), 'role' => 'company']);

        // ④ 企業の実体データ（代理店Aに紐付ける）
        Company::create([
            'agency_id' => $agency->id,
            'user_id' => $companyUser->id,
            'name' => '焼肉 テスト店',
            'address' => '東京都渋谷区...',
            'google_map_url' => 'https://goo.gl/maps/...'
        ]);
    }
}