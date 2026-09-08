<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Review;
use App\Models\Company;
use App\Models\User;
use App\Models\Plan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReviewController extends Controller
{
    // ① 入力画面を表示（履歴も表示）
    public function showForm()
    {
        $user = auth()->user();
        $company = null;
        $reviews = collect();

        if ($user && $user->role === 'company') {
            // ★ selectedSurvey.questions を一緒に読み込む
            $company = Company::with('selectedSurvey.questions')->where('user_id', $user->id)->first();
            if ($company) {
                $reviews = Review::where('company_id', $company->id)->latest()->get();
            }
        }

        return view('form', compact('company', 'reviews'));
    }

    // ② お客様用の専用フォーム表示（QRコードからのアクセス）
    public function showReviewForm($token) // 引数を $id から $token に変更
    {
        // ★ findOrFail($id) から where('token', $token)->firstOrFail() に変更
        $company = Company::with('selectedSurvey.questions')->where('token', $token)->firstOrFail();

        $reviews = collect();
        return view('form', compact('company', 'reviews'));
    }

    // ③ AIで口コミを生成して保存する
    public function generate(Request $request)
    {
        // 1. 店舗とアンケートの特定
        $companyId = $request->input('company_id');
        // ★ 設問情報も一緒に取得するように変更
        $company = \App\Models\Company::with('selectedSurvey.questions')->find($companyId);

        if (!$company) {
            return back()->with('error', '店舗情報が見つかりません。');
        }

        // ==========================================
        // ★ 新規追加：プランの生成回数（今月）の上限チェック
        // ==========================================
        $plan = $company->effectivePlan();

        if ($plan && !is_null($plan->max_reviews_monthly)) {
            $currentMonthCount = \App\Models\Review::where('company_id', $company->id)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();

            if ($currentMonthCount >= $plan->max_reviews_monthly) {
                return back()
                    ->with(
                        'error',
                        'この店舗は今月の口コミ利用上限（' . $plan->max_reviews_monthly . '件）に達しているため、これ以上作成できません。'
                    )
                    ->withInput();
            }
        }
        // ==========================================

        $answers = $request->input('answers', []);

        // ==========================================
        // ★ 必須項目のバリデーション（空欄ブロック）
        // ==========================================
        if ($company->selectedSurvey) {
            foreach ($company->selectedSurvey->questions as $question) {
                if ($question->is_required) {
                    $val = $answers[$question->id] ?? null;

                    // 値が無い、空文字、またはチェックボックスが1つも選ばれていない場合
                    if (is_null($val) || (is_string($val) && trim($val) === '') || (is_array($val) && count($val) === 0)) {
                        return back()->with('error', "「{$question->question_text}」は必須項目です。")->withInput();
                    }
                }
            }
        }

        // 2. APIキーの読み込み
        $apiKey = env('GEMINI_API_KEY');
        $promptDetails = "";

        if (!empty($answers)) {
            $questionIds = array_keys($answers);
            $questions = \App\Models\Question::whereIn('id', $questionIds)->get()->keyBy('id');

            foreach ($answers as $qId => $answerData) {
                $question = $questions->get($qId);
                if ($question) {
                    $answerText = is_array($answerData) ? implode('、', $answerData) : $answerData;
                    if (!empty($answerText)) {
                        $promptDetails .= "【{$question->question_text}】: {$answerText}\n";
                    }
                }
            }
        }

        if (empty(trim($promptDetails))) {
            $promptDetails = "特になし";
        }

        $reviewStyle = 'natural';

        if ($company->effectivePlanCode() === 'premium') {
            $reviewStyle = $company->review_style ?? 'natural';
        }

        $styleInstruction = match ($reviewStyle) {
            'polite' => '丁寧で、きちんとした口調で書いてください。礼儀正しく自然な文章にしてください。',
            'passionate' => '感動や満足感がしっかり伝わる、熱量の高い口調で書いてください。ただし大げさすぎたり、不自然な表現にならないようにしてください。',
            default => '親しみやすく、堅すぎない自然体の口調で書いてください。',
        };

        $prompt = "以下の情報を元に、Googleマップ用の素敵な口コミを200文字程度で作成してください。\n" .
        "【重要ルール】\n" .
        "・挨拶や「作成しました」などの前置き、後書きは一切書かないでください。\n" .
        "・「口コミ本文」などの見出しも不要です。口コミの文章だけを出力してください。\n" .
        "・{$styleInstruction}\n\n" .
        "[店舗名]: {$company->name}\n" .
        "[お客様の感想]:\n{$promptDetails}";

        // 3. API通信
        $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        $data = $response->json();

        // 4. エラーハンドリング
        if (isset($data['error'])) {
            $msg = $data['error']['message'] ?? '通信エラー';

            if (str_contains(strtolower($msg), 'high demand') || str_contains(strtolower($msg), 'overloaded')) {
                return back()->with('error', '現在、利用者が多く混み合っています。1〜2分待ってからもう一度お試しください。')->withInput();
            }

            return back()->with('error', "Google APIエラー: {$msg}")->withInput();
        }

        $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '文章の生成に失敗しました。';

        // 5. データベースに保存
        // ★修正：duration_seconds（所要時間）の保存処理を追加
        $review = \App\Models\Review::create([
            'company_id' => $company->id,
            'prompt_details' => $promptDetails,
            'generated_text' => $aiText,
            'duration_seconds' => $request->input('duration_seconds', 0) // ★ここを追加
        ]);

        return view('result', compact('aiText', 'company', 'review')); // ★ result画面で track 用に $review を渡すように追加
    }

// ④ ダッシュボード表示
// ④ ダッシュボード表示
public function dashboard(Request $request)
{
    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | 管理者ダッシュボード
    |--------------------------------------------------------------------------
    */
    if ($user->role === 'admin') {

        $companyCount = Company::count();

        $companies = Company::with('agency')
            ->latest()
            ->get();

        $activeCount = Company::whereNotNull('plan_id')->count();

        $inactiveCount = Company::whereNull('plan_id')->count();

        return view('admin_dashboard', compact(
            'companyCount',
            'companies',
            'activeCount',
            'inactiveCount'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | 店舗ダッシュボード
    |--------------------------------------------------------------------------
    */
    $myCompany = Company::where('user_id', $user->id)->first();


    // 店舗情報が存在しない場合
    if (!$myCompany) {
        return view('company_dashboard', [
            'myCompany' => null,
            'latestReviews' => collect(),
            'stats' => [
                'total_count' => 0,
                'copy_rate' => 0,
                'redirect_rate' => 0,
                'avg_duration' => 0,
            ],
            'surveyStats' => [],
            'reviewUrl' => null,
            'filter' => 'this_month',
            'notices' => collect(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | レビュー集計
    |--------------------------------------------------------------------------
    */

    $filter = $request->input('filter', 'this_month');

    $query = Review::where('company_id', $myCompany->id);

    if ($filter === 'last_month') {

        $query->whereMonth(
            'created_at',
            now()->subMonth()->month
        );

    } elseif ($filter === 'last_3_months') {

        $query->where(
            'created_at',
            '>=',
            now()->subMonths(3)
        );

    } elseif (
        $filter === 'custom'
        && $request->start_date
        && $request->end_date
    ) {

        $query->whereBetween(
            'created_at',
            [
                $request->start_date,
                $request->end_date
            ]
        );

    } else {

        $query->whereMonth(
            'created_at',
            now()->month
        );
    }

    $filteredReviews = $query->get();

    /*
    |--------------------------------------------------------------------------
    | 分析指標
    |--------------------------------------------------------------------------
    */

    $totalCount = $filteredReviews->count();

    $copyCount = $filteredReviews
        ->where('is_copied', true)
        ->count();

    $redirectCount = $filteredReviews
        ->where('is_redirected', true)
        ->count();

    $avgDuration = $filteredReviews->avg('duration_seconds');

    $stats = [
        'total_count' => $totalCount,

        'copy_rate' => $totalCount > 0
            ? round(($copyCount / $totalCount) * 100, 1)
            : 0,

        'redirect_rate' => $totalCount > 0
            ? round(($redirectCount / $totalCount) * 100, 1)
            : 0,

        'avg_duration' => $avgDuration
            ? round($avgDuration / 60, 1)
            : 0,
    ];

    /*
    |--------------------------------------------------------------------------
    | アンケート集計
    |--------------------------------------------------------------------------
    */

    $surveyStats = [];

    $myCompany->load('selectedSurvey.questions');

    if ($myCompany->selectedSurvey) {

        foreach ($myCompany->selectedSurvey->questions as $question) {

            if (
                !in_array(
                    $question->type,
                    ['radio', 'checkbox']
                )
            ) {
                continue;
            }

            $options = is_string($question->options)
                ? json_decode($question->options, true)
                : $question->options;

            $counts = [];

            if (is_array($options)) {

                foreach ($options as $option) {
                    $counts[$option] = 0;
                }
            }

            $filteredReviews->each(
                function ($review) use (
                    $question,
                    &$counts
                ) {

                    if (
                        preg_match(
                            "/【" .
                            preg_quote(
                                $question->question_text,
                                '/'
                            ) .
                            "】: (.*?)\n/",
                            $review->prompt_details,
                            $matches
                        )
                    ) {

                        $answerText = $matches[1] ?? '';

                        $selectedItems = explode(
                            '、',
                            $answerText
                        );

                        foreach ($selectedItems as $item) {

                            $item = trim($item);

                            if ($item === '') {
                                continue;
                            }

                            if (isset($counts[$item])) {
                                $counts[$item]++;
                            } else {
                                $counts[$item] = 1;
                            }
                        }
                    }
                }
            );

            $surveyStats[
                $question->question_text
            ] = $counts;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 最新レビュー
    |--------------------------------------------------------------------------
    */

    $latestReviews = Review::where(
        'company_id',
        $myCompany->id
    )
        ->latest()
        ->take(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | お知らせ
    |--------------------------------------------------------------------------
    */

    $notices = \App\Models\Notice::with('category')
        ->whereIn(
            'target_role',
            ['all', 'company']
        )
        ->latest()
        ->take(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 口コミフォームURL
    |--------------------------------------------------------------------------
    */

    $reviewUrl = route(
        'review.show',
        $myCompany->token
    );

    /*
    |--------------------------------------------------------------------------
    | 店舗ダッシュボード
    |--------------------------------------------------------------------------
    */

    return view(
        'company_dashboard',
        compact(
            'myCompany',
            'latestReviews',
            'stats',
            'surveyStats',
            'reviewUrl',
            'filter',
            'notices'
        )
    );
}

    // ⑤ 設定画面表示
    public function showSettings()
    {
        $company = Company::where('user_id', auth()->id())->first();

        if (!$company) {
            return "店舗データなし";
        }

        $plans = Plan::orderBy('base_price')->get();

        return view('company_settings', compact('company', 'plans'));
    }

    // ⑥ 設定更新
    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $company = \App\Models\Company::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // $data = $request->only(['name', 'address', 'google_map_url', 'welcome_message', 'completion_message']);
        $data = $request->only([
            'name',
            'address',
            'google_map_url',
            'welcome_message',
            'completion_message',
            'theme_color',
        ]);

        if ($company->effectivePlanCode() === 'premium') {
            $request->validate([
                'review_style' => 'required|in:natural,polite,passionate',
            ]);

            $data['review_style'] = $request->input('review_style');
        } else {
            // プレミアム以外では必ず自然体に戻す
            $data['review_style'] = 'natural';
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        $company->update($data);

        return back()->with('success', '設定を保存しました。');
    }

    // 企業向け：口コミ一覧画面
    public function index()
    {
        $user = auth()->user();
        $myCompany = \App\Models\Company::where('user_id', $user->id)->firstOrFail();

        $reviews = \App\Models\Review::where('company_id', $myCompany->id)
                    ->latest()
                    ->paginate(20);

        return view('reviews_index', compact('myCompany', 'reviews'));
    }

    // 計測用メソッド
    public function track(Request $request, $id)
    {
        $review = \App\Models\Review::findOrFail($id);

        if ($request->action === 'copy') {
            $review->is_copied = true;
        } elseif ($request->action === 'direct_post') {
            // 「そのまま投稿」の場合は、コピー済・遷移済・そのまま投稿済 の全てをtrueにする
            $review->is_copied = true;
            $review->is_redirected = true;
            $review->is_direct_post = true;
        }

        $review->save();

        return response()->json(['status' => 'success']);
    }


    public function exportDashboardCsv(Request $request)
{
    $user = auth()->user();

    $company = Company::where('user_id', $user->id)->firstOrFail();

    // プレミアムプラン限定
    abort_unless(
        $company->effectivePlanCode() === 'premium',
        403
    );

    $filter = $request->input('filter', 'this_month');

    if ($filter === 'last_month') {
        $startDate = now()->subMonthNoOverflow()->startOfMonth();
        $endDate = now()->subMonthNoOverflow()->endOfMonth();
    } elseif ($filter === 'last_3_months') {
        $startDate = now()->subMonths(3)->startOfDay();
        $endDate = now()->endOfDay();
    } elseif (
        $filter === 'custom'
        && $request->filled('start_date')
        && $request->filled('end_date')
    ) {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
    } else {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
    }

    $reviews = Review::where('company_id', $company->id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    $filename = 'dashboard_' .
        $startDate->format('Ymd') .
        '_' .
        $endDate->format('Ymd') .
        '.csv';

    return response()->streamDownload(function () use (
        $reviews,
        $startDate,
        $endDate
    ) {
        $handle = fopen('php://output', 'w');

        // Excelでの文字化け防止
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            '期間',
            '回答数',
            'コピー率',
            'Google遷移率',
            '平均所要時間',
        ]);

        $currentDate = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($currentDate->lte($lastDate)) {
            $date = $currentDate->format('Y-m-d');

            $dailyReviews = $reviews->filter(function ($review) use ($date) {
                return $review->created_at->format('Y-m-d') === $date;
            });

            $totalCount = $dailyReviews->count();
            $copyCount = $dailyReviews->where('is_copied', true)->count();
            $redirectCount = $dailyReviews->where('is_redirected', true)->count();
            $avgDuration = $dailyReviews->avg('duration_seconds');

            $copyRate = $totalCount > 0
                ? round(($copyCount / $totalCount) * 100, 1)
                : 0;

            $redirectRate = $totalCount > 0
                ? round(($redirectCount / $totalCount) * 100, 1)
                : 0;

            $avgDurationMinutes = $avgDuration
                ? round($avgDuration / 60, 1)
                : 0;

            fputcsv($handle, [
                $currentDate->format('Y/m/d'),
                $totalCount,
                $copyRate . '%',
                $redirectRate . '%',
                $avgDurationMinutes . '分',
            ]);

            $currentDate->addDay();
        }

        // 最終行：ダッシュボードと同じ期間全体の集計
        $totalCount = $reviews->count();
        $copyCount = $reviews->where('is_copied', true)->count();
        $redirectCount = $reviews->where('is_redirected', true)->count();
        $avgDuration = $reviews->avg('duration_seconds');

        $copyRate = $totalCount > 0
            ? round(($copyCount / $totalCount) * 100, 1)
            : 0;

        $redirectRate = $totalCount > 0
            ? round(($redirectCount / $totalCount) * 100, 1)
            : 0;

        $avgDurationMinutes = $avgDuration
            ? round($avgDuration / 60, 1)
            : 0;

        fputcsv($handle, [
            $startDate->format('Y/m/d') . '～' . $endDate->format('Y/m/d'),
            $totalCount,
            $copyRate . '%',
            $redirectRate . '%',
            $avgDurationMinutes . '分',
        ]);

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}

    // QRダウンロード
    public function downloadQr()
    {
        $user = auth()->user();

        $company = Company::where('user_id', $user->id)->firstOrFail();

        $reviewUrl = route('review.show', $company->token);

        $png = (string) QrCode::format('png')
            ->size(500)
            ->margin(2)
            ->generate($reviewUrl);

        return response()->streamDownload(
            function () use ($png) {
                echo $png;
            },
            'review_qr.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }

}