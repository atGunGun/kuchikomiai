<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeController extends Controller
{
    /**
     * Stripe Checkout画面へ移動
     */
    public function checkout(Request $request, Plan $plan)
    {
        // 無料プランはStripe決済不要
        if ($plan->base_price <= 0 || empty($plan->stripe_price_id)) {
            return redirect()
                ->route('settings.edit')
                ->with('error', 'このプランは決済不要です。');
        }

        // Stripe APIキー
        Stripe::setApiKey(config('services.stripe.secret'));

        // ログイン中のユーザー
        $user = auth()->user();

        // Stripe Checkout Sessionを作成
        $session = Session::create([
            'mode' => 'subscription',

            'customer_email' => $user->email,

            'line_items' => [
                [
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ],
            ],

            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),

            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
        ]);

        return redirect($session->url);
    }

    /**
     * 決済成功
     */
    public function success(Request $request)
    {
        // session_id がない場合
        if (!$request->session_id) {
            return redirect()
                ->route('settings.edit')
                ->with('error', '決済情報を確認できませんでした。');
        }

        // Stripe APIキー
        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkout SessionをStripeから取得
        $session = Session::retrieve($request->session_id);

        // metadataから情報を取得
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        if (!$userId || !$planId) {
            return redirect()
                ->route('settings.edit')
                ->with('error', '契約情報を確認できませんでした。');
        }

        // Companyを取得
        $company = \App\Models\Company::where('user_id', $userId)->first();

        if (!$company) {
            return redirect()
                ->route('settings.edit')
                ->with('error', '店舗情報が見つかりませんでした。');
        }

        // Planを取得
        $plan = Plan::find($planId);

        if (!$plan) {
            return redirect()
                ->route('settings.edit')
                ->with('error', 'プラン情報が見つかりませんでした。');
        }

        // Companyの契約プランを変更
        $company->update([
            'plan_id' => $plan->id,
            'applied_price' => $plan->base_price,
        ]);

        return redirect()
            ->route('settings.edit')
            ->with('success', $plan->name . 'への変更が完了しました。');
    }

    /**
     * 決済キャンセル
     */
    public function cancel()
    {
        return redirect()
            ->route('settings.edit')
            ->with('error', '決済がキャンセルされました。');
    }

    /**
 * 無料プランへ変更
 */
public function freePlan()
{
    $user = auth()->user();

    $company = \App\Models\Company::where('user_id', $user->id)->first();

    if (!$company) {
        return redirect()
            ->route('settings.edit')
            ->with('error', '店舗情報が見つかりませんでした。');
    }

    $freePlan = Plan::where('code', 'free')->firstOrFail();

    // すでに無料プランの場合
    if ($company->plan_id === $freePlan->id) {
        return redirect()
            ->route('settings.edit')
            ->with('error', 'すでに無料プランです。');
    }

    // Stripeのサブスクリプションがある場合は解約
    if (!empty($company->stripe_subscription_id)) {

        Stripe::setApiKey(config('services.stripe.secret'));

        try {

            $stripe = new \Stripe\StripeClient(
                config('services.stripe.secret')
            );

            $stripe->subscriptions->cancel(
                $company->stripe_subscription_id
            );

            $company->update([
                'plan_id' => $freePlan->id,
                'applied_price' => $freePlan->base_price,
                'stripe_subscription_id' => null,
            ]);

        } catch (\Exception $e) {

            Log::error('Stripe 無料プラン変更時の解約エラー', [
                'company_id' => $company->id,
                'subscription_id' => $company->stripe_subscription_id,
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('settings.edit')
                ->with('error', 'Stripeのサブスクリプション解約に失敗しました。');
        }

    } else {

        // Stripe契約が存在しない場合は直接無料プランへ
        $company->update([
            'plan_id' => $freePlan->id,
            'applied_price' => $freePlan->base_price,
            'stripe_subscription_id' => null,
        ]);
    }

    return redirect()
        ->route('settings.edit')
        ->with('success', '無料プランに変更しました。');
}

/**
 * Stripe Webhook
 */
public function webhook(Request $request)
{
    $payload = $request->getContent();
    $signature = $request->header('Stripe-Signature');

    $webhookSecret = config('services.stripe.webhook_secret');

    try {
        $event = Webhook::constructEvent(
            $payload,
            $signature,
            $webhookSecret
        );
    } catch (\UnexpectedValueException $e) {

        Log::error('Stripe Webhook: 不正なPayload');

        return response('Invalid payload', 400);

    } catch (SignatureVerificationException $e) {

        Log::error('Stripe Webhook: 署名検証失敗');

        return response('Invalid signature', 400);
    }

    Log::info('Stripe Webhook received', [
        'type' => $event->type,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Checkout完了
    |--------------------------------------------------------------------------
    */
    if ($event->type === 'checkout.session.completed') {

        $session = $event->data->object;

        // metadataからユーザーID・プランIDを取得
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;

        Log::info('Stripe Checkout completed', [
            'user_id' => $userId,
            'plan_id' => $planId,
            'session_id' => $session->id,
        ]);

        // 必要な情報がなければ終了
        if (!$userId || !$planId) {
            Log::error('Stripe Webhook: metadataがありません', [
                'session_id' => $session->id,
            ]);

            return response()->json([
                'status' => 'error',
            ], 400);
        }

        // Companyを取得
        $company = \App\Models\Company::where('user_id', $userId)->first();

        if (!$company) {
            Log::error('Stripe Webhook: Companyが見つかりません', [
                'user_id' => $userId,
            ]);

            return response()->json([
                'status' => 'error',
            ], 404);
        }

        // Planを取得
        $plan = Plan::find($planId);

        if (!$plan) {
            Log::error('Stripe Webhook: Planが見つかりません', [
                'plan_id' => $planId,
            ]);

            return response()->json([
                'status' => 'error',
            ], 404);
        }

        // Companyの契約プランを更新
        $company->update([
            'plan_id' => $plan->id,
            'applied_price' => $plan->base_price,
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
        ]);

        Log::info('Stripe Webhook: プラン更新完了', [
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'applied_price' => $plan->base_price,
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | サブスクリプション解約
    |--------------------------------------------------------------------------
    */
    if ($event->type === 'customer.subscription.deleted') {

        $subscription = $event->data->object;

        // StripeのSubscription IDからCompanyを取得
        $company = \App\Models\Company::where(
            'stripe_subscription_id',
            $subscription->id
        )->first();

        if (!$company) {
            Log::error('Stripe Webhook: 解約対象のCompanyが見つかりません', [
                'stripe_subscription_id' => $subscription->id,
            ]);

            return response()->json([
                'status' => 'error',
            ], 404);
        }

        $freePlan = Plan::where('code', 'free')->firstOrFail();

        // 無料プランへ戻す
        $company->update([
            'plan_id' => $freePlan->id,
            'applied_price' => $freePlan->base_price,
            'stripe_subscription_id' => null,
        ]);

        Log::info('Stripe Webhook: サブスクリプション解約処理完了', [
            'company_id' => $company->id,
            'stripe_subscription_id' => $subscription->id,
            'plan_id' => $freePlan->id,
        ]);
    }

    return response()->json([
        'status' => 'success'
    ]);
}


}