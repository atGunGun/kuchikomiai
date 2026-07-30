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
        if ($company->plan && $company->plan->max_generations > 0) {
            // この店舗の「今月」の生成回数をカウント
            $currentMonthCount = \App\Models\Review::where('company_id', $company->id)
                                    ->whereMonth('created_at', now()->month)
                                    ->count();
                                    
            if ($currentMonthCount >= $company->plan->max_generations) {
                // 上限に達していたらエラーメッセージと共に前の画面に戻す
                return back()->with('error', 'この店舗は今月のAI生成上限（' . $company->plan->max_generations . '回）に達しているため、これ以上作成できません。')->withInput();
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

        $prompt = "以下の情報を元に、Googleマップ用の素敵な口コミを200文字程度で作成してください。\n" .
                "【重要ルール】\n" .
                "・挨拶や「作成しました」などの前置き、後書きは一切書かないでください。\n" .
                "・「口コミ本文」などの見出しも不要です。口コミの文章だけを出力してください。\n\n" .
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
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $myCompany = \App\Models\Company::where('user_id', $user->id)->first();

        // 管理者（運営）とみなして専用画面を表示
        if (!$myCompany) {
            $companyCount = \App\Models\Company::count();
            $companies = \App\Models\Company::with('agency')->latest()->get();

            $activeCount = \App\Models\Company::whereNotNull('plan_id')->count();
            $inactiveCount = \App\Models\Company::whereNull('plan_id')->count();
            
            return view('admin_dashboard', compact('companyCount', 'companies', 'activeCount', 'inactiveCount'));
        }

        // 店舗ユーザーとして分析画面を表示
        $filter = $request->input('filter', 'this_month');
        $query = \App\Models\Review::where('company_id', $myCompany->id);

        if ($filter === 'last_month') {
            $query->whereMonth('created_at', now()->subMonth()->month);
        } elseif ($filter === 'last_3_months') {
            $query->where('created_at', '>=', now()->subMonths(3));
        } elseif ($filter === 'custom' && $request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } else { 
            $query->whereMonth('created_at', now()->month);
        }

        $filteredReviews = $query->get();

        // 分析指標の計算
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

        // アンケート別・項目別集計
        $surveyStats = [];
        if ($myCompany->selectedSurvey) {
            foreach ($myCompany->selectedSurvey->questions as $question) {
                if (in_array($question->type, ['radio', 'checkbox'])) {
                    
                    // ★ 修正：設問の元々の「選択肢（options）」をベースにして0件でも表示されるようにする
                    $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                    $counts = [];
                    if (is_array($options)) {
                        foreach ($options as $opt) {
                            $counts[$opt] = 0; // すべての選択肢をまず0回でセット
                        }
                    }

                    // 回答データを分解してカウントアップ
                    $filteredReviews->each(function($r) use ($question, &$counts) {
                        if (preg_match("/【{$question->question_text}】: (.*?)\n/", $r->prompt_details, $matches)) {
                            $answerText = $matches[1] ?? '';
                            // チェックボックスの「A、B」を分解して個別にカウント
                            $selectedItems = explode('、', $answerText);
                            foreach ($selectedItems as $item) {
                                $item = trim($item);
                                if ($item !== '') {
                                    if (isset($counts[$item])) {
                                        $counts[$item]++;
                                    } else {
                                        $counts[$item] = 1;
                                    }
                                }
                            }
                        }
                    });
                    
                    $surveyStats[$question->question_text] = $counts;
                }
            }
        }

        // 最新5件のみ取得
        $latestReviews = \App\Models\Review::where('company_id', $myCompany->id)
                            ->latest()->take(5)->get();

        // ★ 追加：公開中のお知らせを取得
        // $notices = \App\Models\Notice::where('is_published', true)->latest()->take(5)->get();
        $notices = \App\Models\Notice::with('category')
            ->whereIn('target_role', ['all', 'company'])
            ->latest()
            ->take(5)
            ->get();

        $reviewUrl = route('review.show', $myCompany->token);

        // ★ 修正：compactに 'notices' を追加してBladeへ渡す
        return view('company_dashboard', compact('myCompany', 'latestReviews', 'stats', 'surveyStats', 'reviewUrl', 'filter', 'notices'));
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
        $user = auth()->user();
        $company = \App\Models\Company::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // $data = $request->only(['name', 'address', 'google_map_url', 'welcome_message', 'completion_message']);
        $data = $request->only(['name', 'address', 'google_map_url', 'welcome_message', 'completion_message', 'theme_color']); // theme_color を追加

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
}