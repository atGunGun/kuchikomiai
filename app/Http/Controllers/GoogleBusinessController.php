<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

class GoogleBusinessController extends Controller
{
    public function redirect()
    {
        $company = auth()->user()->company;

        abort_unless(
            $company && $company->effectivePlanCode() === 'premium',
            403
        );

        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/business.manage',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }

    public function callback()
    {
        $company = auth()->user()->company;

        abort_unless(
            $company && $company->effectivePlanCode() === 'premium',
            403
        );

        $googleUser = Socialite::driver('google')->user();

        $company->google_access_token = $googleUser->token;

        if ($googleUser->refreshToken) {
            $company->google_refresh_token = $googleUser->refreshToken;
        }

        $company->google_token_expires_at = $googleUser->expiresIn
            ? now()->addSeconds($googleUser->expiresIn)
            : null;

        $company->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Googleアカウントとの認証に成功しました。');
    }
}