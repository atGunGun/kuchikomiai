<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Review;
use App\Models\Company;
use App\Models\User;

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
    public function showReviewForm($id)
    {
        // ★ selectedSurvey.questions を一緒に読み込む
        $company = Company::with('selectedSurvey.questions')->findOrFail($id);
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

        $answers = $request->input('answers', []);

        // ==========================================
        // ★ 追加：必須項目のバリデーション（空欄ブロック）
        // ==========================================
        if ($company->selectedSurvey) {
            foreach ($company->selectedSurvey->questions as $question) {
                if ($question->is_required) {
                    $val = $answers[$question->id] ?? null;
                    
                    // 値が無い、空文字、またはチェックボックスが1つも選ばれていない場合
                    if (is_null($val) || (is_string($val) && trim($val) === '') || (is_array($val) && count($val) === 0)) {
                        // エラーメッセージと共に元の画面へ戻す
                        return back()->with('error', "「{$question->question_text}」は必須項目です。")->withInput();
                    }
                }
            }
        }
        // ==========================================

        // 2. APIキーの読み込み
        $apiKey = env('GEMINI_API_KEY');
        $promptDetails = "";

        // 以降は元のコードと同じです
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

        $prompt = "以下の情報を元に、Googleマップ用の素敵な口コミを200文字程度で作成してください。\n" .
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
        \App\Models\Review::create([
            'company_id' => $company->id,
            'prompt_details' => $promptDetails, 
            'generated_text' => $aiText
        ]);

        return view('result', compact('aiText', 'company'));
    }

    // ④ ダッシュボード表示
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $myCompany = \App\Models\Company::where('user_id', $user->id)->firstOrFail();

        // --- 1. 期間絞り込みロジック ---
        $filter = $request->input('filter', 'this_month'); // デフォルトは今月
        $query = \App\Models\Review::where('company_id', $myCompany->id);

        if ($filter === 'last_month') {
            $query->whereMonth('created_at', now()->subMonth()->month);
        } elseif ($filter === 'last_3_months') {
            $query->where('created_at', '>=', now()->subMonths(3));
        } elseif ($filter === 'custom' && $request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } else { // this_month
            $query->whereMonth('created_at', now()->month);
        }

        $filteredReviews = $query->get();

        // --- 2. 分析指標の計算 ---
        $totalCount = $filteredReviews->count();
        $copyCount = $filteredReviews->where('is_copied', true)->count();
        $redirectCount = $filteredReviews->where('is_redirected', true)->count();
        $avgDuration = $filteredReviews->avg('duration_seconds');

        $stats = [
            'total_count' => $totalCount,
            'copy_rate' => $totalCount > 0 ? round(($copyCount / $totalCount) * 100, 1) : 0,
            'redirect_rate' => $totalCount > 0 ? round(($redirectCount / $totalCount) * 100, 1) : 0,
            'avg_duration' => $avgDuration ? round($avgDuration / 60, 1) : 0,
        ];

        // --- 3. アンケート別・項目別集計 ---
        // ここでは現在のアンケート(selectedSurvey)の集計を行う例
        $surveyStats = [];
        if ($myCompany->selectedSurvey) {
            foreach ($myCompany->selectedSurvey->questions as $question) {
                if (in_array($question->type, ['radio', 'checkbox'])) {
                    // 回答をパースして集計
                    $answers = $filteredReviews->map(function($r) use ($question) {
                        // prompt_detailsから該当の設問回答を抽出する簡易ロジック
                        // 本来は回答専用のテーブルを作るのが理想ですが、現状の構造に合わせます
                        preg_match("/【{$question->question_text}】: (.*?)\n/", $r->prompt_details, $matches);
                        return $matches[1] ?? null;
                    })->filter()->flatMap(fn($item) => explode('、', $item));
                    
                    $surveyStats[$question->question_text] = $answers->countBy();
                }
            }
        }

        // --- 4. 最新5件のみ取得 ---
        $latestReviews = \App\Models\Review::where('company_id', $myCompany->id)
                            ->latest()->take(5)->get();

        $reviewUrl = route('review.show', $myCompany->id);

        return view('company_dashboard', compact('myCompany', 'latestReviews', 'stats', 'surveyStats', 'reviewUrl', 'filter'));
    }

    // ⑤ 設定画面表示
    public function showSettings()
    {
        $company = Company::where('user_id', auth()->id())->first();
        if (!$company) return "店舗データなし";
        return view('company_settings', compact('company'));
    }

    // ⑥ 設定更新
    public function updateSettings(Request $request)
    {
        $company = Company::where('user_id', auth()->id())->first();
        if ($company) {
            $company->update($request->all());
        }
        return back()->with('success', '設定を保存しました！');
    }

    // 企業向け：口コミ一覧画面
    public function index()
    {
        $user = auth()->user();
        $myCompany = \App\Models\Company::where('user_id', $user->id)->firstOrFail();

        // 最新順に1ページ20件ずつ取得（ページネーション）
        $reviews = \App\Models\Review::where('company_id', $myCompany->id)
                    ->latest()
                    ->paginate(20);

        return view('reviews_index', compact('myCompany', 'reviews'));
    }
}