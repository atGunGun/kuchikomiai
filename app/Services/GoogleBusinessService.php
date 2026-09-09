<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleBusinessService
{
    public function getAccessToken(Company $company): string
    {
        if (
            $company->google_access_token &&
            $company->google_token_expires_at &&
            $company->google_token_expires_at->isFuture()
        ) {
            return $company->google_access_token;
        }

        if (!$company->google_refresh_token) {
            throw new RuntimeException(
                'Google Business Profileの再連携が必要です。'
            );
        }

        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $company->google_refresh_token,
                'grant_type' => 'refresh_token',
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Googleアクセストークンの更新に失敗しました。'
            );
        }

        $data = $response->json();

        if (empty($data['access_token'])) {
            throw new RuntimeException(
                'Googleからアクセストークンを取得できませんでした。'
            );
        }

        $company->google_access_token = $data['access_token'];
        $company->google_token_expires_at = now()->addSeconds(
            $data['expires_in'] ?? 3600
        );
        $company->save();

        return $company->google_access_token;
    }
}