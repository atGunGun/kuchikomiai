<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\SurveyTemplate;
use App\Models\SurveyTemplateQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSurveyTemplateController extends Controller
{
    /**
     * 業種・テンプレート一覧
     */
    public function index()
    {
        $industries = Industry::with('surveyTemplates.questions')
            ->orderBy('name')
            ->get();

        return view('admin.survey_templates.index', compact('industries'));
    }

    /**
     * 業種作成
     */
    public function storeIndustry(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Industry::create([
            'name' => $request->name,
        ]);

        return back()->with('success', '業種を追加しました。');
    }

    /**
     * テンプレート作成画面
     */
    public function createTemplate(Industry $industry)
    {
        return view(
            'admin.survey_templates.create',
            compact('industry')
        );
    }

    /**
     * テンプレート保存
     */
    public function storeTemplate(Request $request, Industry $industry)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,textarea,checkbox,radio',
        ]);

        DB::transaction(function () use ($request, $industry) {

            $template = SurveyTemplate::create([
                'industry_id' => $industry->id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            foreach ($request->questions as $index => $q) {

                $options = null;

                if (
                    in_array($q['type'], ['checkbox', 'radio'])
                    && !empty($q['options'])
                    && is_array($q['options'])
                ) {
                    $options = array_values(
                        array_filter(
                            $q['options'],
                            fn ($value) => trim($value) !== ''
                        )
                    );
                }

                SurveyTemplateQuestion::create([
                    'survey_template_id' => $template->id,
                    'question_text' => $q['text'],
                    'type' => $q['type'],
                    'is_required' => isset($q['is_required']),
                    'sort_order' => $index,
                    'options' => $options,
                ]);
            }
        });

        return redirect()
            ->route('admin.survey-templates.index')
            ->with('success', 'テンプレートを作成しました。');
    }

    /**
     * テンプレート編集画面
     */
    public function editTemplate(SurveyTemplate $template)
    {
        $template->load('industry', 'questions');

        return view(
            'admin.survey_templates.edit',
            compact('template')
        );
    }

    /**
     * テンプレート更新
     */
    public function updateTemplate(
        Request $request,
        SurveyTemplate $template
    ) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|in:text,textarea,checkbox,radio',
        ]);

        DB::transaction(function () use ($request, $template) {

            $template->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // 設問を一度削除して再作成
            $template->questions()->delete();

            foreach ($request->questions as $index => $q) {

                $options = null;

                if (
                    in_array($q['type'], ['checkbox', 'radio'])
                    && !empty($q['options'])
                    && is_array($q['options'])
                ) {
                    $options = array_values(
                        array_filter(
                            $q['options'],
                            fn ($value) => trim($value) !== ''
                        )
                    );
                }

                SurveyTemplateQuestion::create([
                    'survey_template_id' => $template->id,
                    'question_text' => $q['text'],
                    'type' => $q['type'],
                    'is_required' => isset($q['is_required']),
                    'sort_order' => $index,
                    'options' => $options,
                ]);
            }
        });

        return redirect()
            ->route('admin.survey-templates.index')
            ->with('success', 'テンプレートを更新しました。');
    }

    /**
     * 業種編集画面
     */
    public function editIndustry(Industry $industry)
    {
        return view(
            'admin.survey_templates.industry_edit',
            compact('industry')
        );
    }

    /**
     * 業種更新
     */
    public function updateIndustry(Request $request, Industry $industry)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $industry->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('admin.survey-templates.index')
            ->with('success', '業種を更新しました。');
    }

    /**
     * 業種削除
     */
    public function destroyIndustry(Industry $industry)
    {
        // この業種にテンプレートが存在する場合は削除させない
        if ($industry->surveyTemplates()->exists()) {
            return back()->with(
                'error',
                'この業種にはテンプレートが登録されているため削除できません。先にテンプレートを削除してください。'
            );
        }

        $industry->delete();

        return redirect()
            ->route('admin.survey-templates.index')
            ->with('success', '業種を削除しました。');
    }

    /**
     * テンプレート削除
     */
    public function destroyTemplate(SurveyTemplate $template)
    {
        $template->delete();

        return back()->with('success', 'テンプレートを削除しました。');
    }
}