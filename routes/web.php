<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminNoticeController; 
use App\Http\Controllers\PublicNoticeController;
use App\Http\Controllers\CompanySurveyController;

// ① アプリの顔（トップページ・お知らせ等）
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [App\Http\Controllers\PublicNoticeController::class, 'top'])->name('top');


// ② エンドユーザー（お客様）向け：口コミ入力・生成（ログイン不要）
// ※元の '/' と被らないように '/form' に変更しました
Route::get('/form', [ReviewController::class, 'showForm'])->name('review.index'); 
Route::post('/generate', [ReviewController::class, 'generate'])->name('review.generate');
// ※店舗ごとの専用QRコード読み込み用
// Route::get('/review/{id}', [ReviewController::class, 'showReviewForm'])->name('review.show');
Route::get('/review/{token}', [ReviewController::class, 'showReviewForm'])->name('review.show');

// ★追加：コピーやGoogle遷移をカウントするAPIルート
Route::post('/review/{id}/track', [ReviewController::class, 'track'])->name('review.track');

// ③ ログインしている人（企業・代理店・運営）専用の安全なエリア
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 共通：ダッシュボード
    Route::get('/dashboard', [ReviewController::class, 'dashboard'])->name('dashboard');

    // 共通：プロフィール管理
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 企業向け：設定画面
    Route::get('/settings', [ReviewController::class, 'showSettings'])->name('settings.edit');
    Route::post('/settings', [ReviewController::class, 'updateSettings'])->name('settings.update');

    // 運営マスタ：プラン管理
    Route::get('/admin/plans', [PlanController::class, 'index'])->name('admin.plans.index');
    Route::post('/admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::delete('/admin/plans/{plan}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');
    
    Route::get('/admin/plans/{plan}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/admin/plans/{plan}', [PlanController::class, 'update'])->name('admin.plans.update');
    
    // 運営マスタ：企業登録・編集
    Route::get('/admin/companies/create', [AdminCompanyController::class, 'create'])->name('admin.companies.create');
    Route::post('/admin/companies/store', [AdminCompanyController::class, 'store'])->name('admin.companies.store');
    Route::get('/admin/companies/{company}/edit', [AdminCompanyController::class, 'edit'])->name('admin.companies.edit');
    Route::put('/admin/companies/{company}', [AdminCompanyController::class, 'update'])->name('admin.companies.update');

    // 運営マスタ：お知らせ管理
    Route::get('/admin/notices', [AdminNoticeController::class, 'index'])->name('admin.notices.index');
    Route::post('/admin/notices/category', [AdminNoticeController::class, 'storeCategory'])->name('admin.notices.category.store');
    Route::get('/admin/notices/create', [AdminNoticeController::class, 'create'])->name('admin.notices.create');
    Route::post('/admin/notices', [AdminNoticeController::class, 'store'])->name('admin.notices.store');
    Route::get('/admin/notices/{notice}/edit', [AdminNoticeController::class, 'edit'])->name('admin.notices.edit');
    Route::put('/admin/notices/{notice}', [AdminNoticeController::class, 'update'])->name('admin.notices.update');

    // 一般公開ページ
    Route::get('/notices', [PublicNoticeController::class, 'index'])->name('notices.index');
    Route::get('/notices/{notice}', [PublicNoticeController::class, 'show'])->name('notices.show');

    // 管理者専用（authグループ内）
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin/settings/global', [AdminNoticeController::class, 'showSettings'])->name('admin.settings.global');
        Route::post('/admin/settings/global', [AdminNoticeController::class, 'updateSettings'])->name('admin.settings.global.update');
    });

    // 企業向け：口コミ一覧（ダッシュボードから「すべて見る」用）
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

    // 企業向け：アンケート管理
    Route::get('/surveys', [CompanySurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/create', [CompanySurveyController::class, 'create'])->name('surveys.create');
    Route::post('/surveys', [CompanySurveyController::class, 'store'])->name('surveys.store');
    Route::delete('/surveys/{survey}', [CompanySurveyController::class, 'destroy'])->name('surveys.destroy');
    Route::get('/surveys/{survey}/edit', [CompanySurveyController::class, 'edit'])->name('surveys.edit');
    Route::put('/surveys/{survey}', [CompanySurveyController::class, 'update'])->name('surveys.update');

    Route::post('/surveys/{survey}/select', [CompanySurveyController::class, 'select'])->name('surveys.select');

});

require __DIR__.'/auth.php';