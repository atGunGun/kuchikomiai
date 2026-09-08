<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminNoticeController;
use App\Http\Controllers\PublicNoticeController;
use App\Http\Controllers\CompanySurveyController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AdminSurveyTemplateController;
use App\Http\Controllers\ContactController;


/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/

// トップページ
Route::get('/', [PublicNoticeController::class, 'top'])
    ->name('top');

/*
|--------------------------------------------------------------------------
| お問い合わせ
|--------------------------------------------------------------------------
*/

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact.index');


// Stripe Webhook
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])
    ->name('stripe.webhook');


/*
|--------------------------------------------------------------------------
| お客様向け（ログイン不要）
|--------------------------------------------------------------------------
*/

// 口コミ入力
Route::get('/form', [ReviewController::class, 'showForm'])
    ->name('review.index');

Route::post('/generate', [ReviewController::class, 'generate'])
    ->name('review.generate');

// 店舗ごとの専用QRコード
Route::get('/review/{token}', [ReviewController::class, 'showReviewForm'])
    ->name('review.show');

// コピー・Google遷移などのカウント
Route::post('/review/{id}/track', [ReviewController::class, 'track'])
    ->name('review.track');


/*
|--------------------------------------------------------------------------
| ログインユーザー共通
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ダッシュボード
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [ReviewController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/dashboard/export-csv', [ReviewController::class, 'exportDashboardCsv'])
        ->name('dashboard.export-csv');

    /*
    |--------------------------------------------------------------------------
    | プロフィール
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | 店舗設定
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [ReviewController::class, 'showSettings'])
        ->name('settings.edit');

    Route::post('/settings', [ReviewController::class, 'updateSettings'])
        ->name('settings.update');


    /*
    |--------------------------------------------------------------------------
    | 口コミ一覧
    |--------------------------------------------------------------------------
    */

    Route::get('/reviews', [ReviewController::class, 'index'])
        ->name('reviews.index');

    /*
    |--------------------------------------------------------------------------
    | QRコードダウンロード
    |--------------------------------------------------------------------------
    */

    Route::get('/qr/download', [ReviewController::class, 'downloadQr'])
        ->name('company.qr.download');

    /*
    |--------------------------------------------------------------------------
    | アンケート管理（店舗）
    |--------------------------------------------------------------------------
    */

    Route::get('/surveys', [CompanySurveyController::class, 'index'])
        ->name('surveys.index');

    Route::get('/surveys', [CompanySurveyController::class, 'index'])
        ->name('surveys.index');

    Route::get('/surveys/create', [CompanySurveyController::class, 'create'])
        ->name('surveys.create');

    Route::post('/surveys', [CompanySurveyController::class, 'store'])
        ->name('surveys.store');

    Route::get('/surveys/{survey}/edit', [CompanySurveyController::class, 'edit'])
        ->name('surveys.edit');

    Route::put('/surveys/{survey}', [CompanySurveyController::class, 'update'])
        ->name('surveys.update');

    Route::delete('/surveys/{survey}', [CompanySurveyController::class, 'destroy'])
        ->name('surveys.destroy');

    Route::post('/surveys/{survey}/select', [CompanySurveyController::class, 'select'])
        ->name('surveys.select');

    // 業種テンプレートから店舗専用アンケートを作成
    Route::post(
        '/surveys/templates/{template}/use',
        [CompanySurveyController::class, 'useTemplate']
    )->name('surveys.templates.use');

    /*
    |--------------------------------------------------------------------------
    | Stripe決済
    |--------------------------------------------------------------------------
    */

    Route::get('/stripe/checkout/{plan}', [StripeController::class, 'checkout'])
        ->name('stripe.checkout');

    Route::get('/stripe/success', [StripeController::class, 'success'])
        ->name('stripe.success');

    Route::get('/stripe/cancel', [StripeController::class, 'cancel'])
        ->name('stripe.cancel');

    Route::post('/stripe/free-plan', [StripeController::class, 'freePlan'])
        ->name('stripe.free-plan');


    /*
    |--------------------------------------------------------------------------
    | 運営管理
    |--------------------------------------------------------------------------
    |
    | 現在は role = admin のユーザーを管理者として扱う。
    |
    */


    /*
    |--------------------------------------------------------------------------
    | プラン管理
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/plans', [PlanController::class, 'index'])
        ->name('admin.plans.index');

    Route::post('/admin/plans', [PlanController::class, 'store'])
        ->name('admin.plans.store');

    Route::get('/admin/plans/{plan}/edit', [PlanController::class, 'edit'])
        ->name('admin.plans.edit');

    Route::put('/admin/plans/{plan}', [PlanController::class, 'update'])
        ->name('admin.plans.update');

    Route::delete('/admin/plans/{plan}', [PlanController::class, 'destroy'])
        ->name('admin.plans.destroy');


    /*
    |--------------------------------------------------------------------------
    | 企業管理
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/companies/create', [AdminCompanyController::class, 'create'])
        ->name('admin.companies.create');

    Route::post('/admin/companies/store', [AdminCompanyController::class, 'store'])
        ->name('admin.companies.store');

    Route::get('/admin/companies/{company}/edit', [AdminCompanyController::class, 'edit'])
        ->name('admin.companies.edit');

    Route::put('/admin/companies/{company}', [AdminCompanyController::class, 'update'])
        ->name('admin.companies.update');


    /*
    |--------------------------------------------------------------------------
    | お知らせ管理
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/notices', [AdminNoticeController::class, 'index'])
        ->name('admin.notices.index');

    Route::post('/admin/notices/category', [AdminNoticeController::class, 'storeCategory'])
        ->name('admin.notices.category.store');

    Route::get('/admin/notices/create', [AdminNoticeController::class, 'create'])
        ->name('admin.notices.create');

    Route::post('/admin/notices', [AdminNoticeController::class, 'store'])
        ->name('admin.notices.store');

    Route::get('/admin/notices/{notice}/edit', [AdminNoticeController::class, 'edit'])
        ->name('admin.notices.edit');

    Route::put('/admin/notices/{notice}', [AdminNoticeController::class, 'update'])
        ->name('admin.notices.update');

    Route::delete('/admin/notices/{notice}', [AdminNoticeController::class, 'destroy'])
        ->name('admin.notices.destroy');

    Route::delete(
        '/admin/notices/categories/{category}',
        [AdminNoticeController::class, 'destroyCategory']
    )->name('admin.notices.categories.destroy');


    /*
    |--------------------------------------------------------------------------
    | グローバル設定
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/settings/global', [AdminNoticeController::class, 'showSettings'])
        ->name('admin.settings.global');

    Route::post('/admin/settings/global', [AdminNoticeController::class, 'updateSettings'])
        ->name('admin.settings.global.update');


    /*
    |--------------------------------------------------------------------------
    | 業種別アンケートテンプレート管理
    |--------------------------------------------------------------------------
    |
    | 管理者：
    |
    | 業種
    |   └── テンプレート
    |         └── 設問
    |
    | をWEB画面から管理する。
    |
    */

Route::middleware(['auth', 'admin'])->group(function () {

    // テンプレート一覧
    Route::get(
        '/admin/survey-templates',
        [AdminSurveyTemplateController::class, 'index']
    )->name('admin.survey-templates.index');

    // 業種追加
    Route::post(
        '/admin/survey-templates/industries',
        [AdminSurveyTemplateController::class, 'storeIndustry']
    )->name('admin.survey-templates.industries.store');

    // 業種編集画面
    Route::get(
        '/admin/survey-templates/industries/{industry}/edit',
        [AdminSurveyTemplateController::class, 'editIndustry']
    )->name('admin.survey-templates.industries.edit');

    // 業種更新
    Route::put(
        '/admin/survey-templates/industries/{industry}',
        [AdminSurveyTemplateController::class, 'updateIndustry']
    )->name('admin.survey-templates.industries.update');

    // 業種削除
    Route::delete(
        '/admin/survey-templates/industries/{industry}',
        [AdminSurveyTemplateController::class, 'destroyIndustry']
    )->name('admin.survey-templates.industries.destroy');

    // テンプレート作成画面
    Route::get(
        '/admin/survey-templates/industries/{industry}/create',
        [AdminSurveyTemplateController::class, 'createTemplate']
    )->name('admin.survey-templates.create');

    // テンプレート保存
    Route::post(
        '/admin/survey-templates/industries/{industry}',
        [AdminSurveyTemplateController::class, 'storeTemplate']
    )->name('admin.survey-templates.store');

    // テンプレート編集画面
    Route::get(
        '/admin/survey-templates/{template}/edit',
        [AdminSurveyTemplateController::class, 'editTemplate']
    )->name('admin.survey-templates.edit');

    // テンプレート更新
    Route::put(
        '/admin/survey-templates/{template}',
        [AdminSurveyTemplateController::class, 'updateTemplate']
    )->name('admin.survey-templates.update');

    // テンプレート削除
    Route::delete(
        '/admin/survey-templates/{template}',
        [AdminSurveyTemplateController::class, 'destroyTemplate']
    )->name('admin.survey-templates.destroy');

});

});


/*
|--------------------------------------------------------------------------
| 一般公開のお知らせ
|--------------------------------------------------------------------------
*/

Route::get('/notices', [PublicNoticeController::class, 'index'])
    ->name('notices.index');

Route::get('/notices/{notice}', [PublicNoticeController::class, 'show'])
    ->name('notices.show');


/*
|--------------------------------------------------------------------------
| 認証関連
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';