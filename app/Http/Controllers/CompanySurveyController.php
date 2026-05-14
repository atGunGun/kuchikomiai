<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;

class CompanySurveyController extends Controller
{
    // 1. アンケート一覧画面
    public function index()
    {
        $company = auth()->user()->company;
        $surveys = Survey::where('company_id', $company->id)->latest()->get();
        return view('surveys.index', compact('surveys', 'company'));
    }

    // 2. アンケート作成画面
    public function create()
    {
        $company = auth()->user()->company;
        $plan = $company->plan;
        
        if (!$plan) {
            return redirect()->route('surveys.index')->with('error', '現在プランが設定されていないため、アンケートを作成できません。運営にお問い合わせください。');
        }

        $currentCount = \App\Models\Survey::where('company_id', $company->id)->count();

        if (!is_null($plan->max_surveys) && $currentCount >= $plan->max_surveys) {
            return redirect()->route('surveys.index')->with('error', 'アンケートの作成上限（'.$plan->max_surveys.'個）に達しています。');
        }

        return view('surveys.create');
    }

    // 3. アンケートの保存処理
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,textarea,checkbox,radio',
        ]);

        $company = auth()->user()->company;

        $survey = Survey::create([
            'company_id' => $company->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        foreach ($request->questions as $index => $q) {
            $options = null;
            // チェックボックスの選択肢（配列）を保存する処理
            if (in_array($q['type'], ['checkbox', 'radio']) && !empty($q['options']) && is_array($q['options'])) {
                $options = array_values(array_filter($q['options'], fn($val) => trim($val) !== ''));
            }

            Question::create([
                'survey_id' => $survey->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'is_required' => isset($q['is_required']) ? true : false,
                'sort_order' => $index,
                'options' => $options,
            ]);
        }

        return redirect()->route('surveys.index')->with('success', 'アンケートを作成しました！');
    }

    // 4. アンケートの削除処理（★ここが追加された部分です）
    public function destroy(Survey $survey)
    {
        // 他社のアンケートを勝手に消せないようにする安全対策
        if ($survey->company_id !== auth()->user()->company->id) {
            abort(403);
        }
        
        $survey->delete();
        return redirect()->route('surveys.index')->with('success', 'アンケートを削除しました。');
    }
    // 5. 編集画面の表示
    public function edit(Survey $survey)
    {
        // 自分の企業のアンケートかチェック
        if ($survey->company_id !== auth()->user()->company->id) abort(403);
        
        // 設問データを一緒に読み込んで画面へ渡す
        $survey->load('questions');
        return view('surveys.edit', compact('survey'));
    }

    // 6. 更新処理
    public function update(Request $request, Survey $survey)
    {
        if ($survey->company_id !== auth()->user()->company->id) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,textarea,checkbox,radio',
        ]);

        // アンケート本体の更新
        $survey->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // ★設問は「一度すべて消して、作り直す」のが一番安全で確実な方法です
        $survey->questions()->delete();

        foreach ($request->questions as $index => $q) {
            $options = null;
            if (in_array($q['type'], ['checkbox', 'radio']) && !empty($q['options']) && is_array($q['options'])) {
                $options = array_values(array_filter($q['options'], fn($val) => trim($val) !== ''));
            }

            Question::create([
                'survey_id' => $survey->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'is_required' => isset($q['is_required']) ? true : false,
                'sort_order' => $index,
                'options' => $options,
            ]);
        }

        return redirect()->route('surveys.index')->with('success', 'アンケートを更新しました！');
    }
    // アンケートを「使用中」に設定する
    public function select(Survey $survey)
    {
        $company = auth()->user()->company;

        if ($survey->company_id !== $company->id) abort(403);

        // 企業の「選択中アンケートID」を更新
        $company->update(['selected_survey_id' => $survey->id]);

        return back()->with('success', '「' . $survey->title . '」を店頭用アンケートに設定しました！');
    }
}