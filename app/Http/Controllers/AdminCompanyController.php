<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminCompanyController extends Controller
{
    // 企業登録画面の表示（←これが消えていたためエラーになっていました）
    public function create()
    {
        if (auth()->user()->role !== 'admin') abort(403);

        // 代理店の一覧を取得（roleがagencyのユーザー）
        $agencies = User::where('role', 'agency')->get();
        // プランの一覧を取得
        $plans = Plan::all();

        return view('admin.companies_create', compact('agencies', 'plans'));
    }

    // 企業の保存処理（幽霊データを防ぐトランザクション付き）
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            // トランザクション開始（途中で失敗したら全部元に戻す）
            DB::transaction(function () use ($request) {
                
                // 1. ユーザー作成
                $user = User::create([
                    'name' => $request->company_name . ' 担当者',
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'company',
                ]);

                // 2. プラン定価取得
                $plan = Plan::find($request->plan_id);
                $appliedPrice = $request->filled('applied_price') ? $request->applied_price : $plan->base_price;

                // 3. 企業作成
                Company::create([
                    'user_id' => $user->id,
                    'name' => $request->company_name,
                    'agency_id' => $request->agency_id ?: null, // 空の場合はnullにする
                    'plan_id' => $plan->id,
                    'applied_price' => $appliedPrice,
                ]);
            });

            // 全部成功したらメッセージを出して戻る
            return redirect()->route('admin.companies.create')
                ->with('success', '「' . $request->company_name . '」を正常に登録完了しました！');

        } catch (\Exception $e) {
            // 万が一システムエラーが起きたら、詳細な理由を画面に出す
            return back()->withInput()->withErrors(['system_error' => '保存中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
    // 企業編集画面の表示
    public function edit(Company $company)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $agencies = User::where('role', 'agency')->get();

        $plans = Plan::whereIn('code', ['free', 'standard', 'premium'])
            ->orderBy('base_price')
            ->get();

        $demoPlans = Plan::whereIn('code', ['standard', 'premium'])
            ->orderBy('base_price')
            ->get();

        return view('admin.companies_edit', compact('company', 'agencies', 'plans', 'demoPlans'));
    }

    // 企業の更新処理
    public function update(Request $request, Company $company)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        // 自分のメールアドレス以外で重複がないかをチェック
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $company->user_id,
            'plan_id' => 'required|exists:plans,id',
            'demo_plan_id' => [
                'nullable',
                Rule::exists('plans', 'id')->where(function ($query) {
                    $query->whereIn('code', ['standard', 'premium']);
                }),
            ],
            'demo_expires_at' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($request, $company) {
                
                // 1. ユーザー（ログイン情報）の更新
                $userData = [
                    'name' => $request->company_name . ' 担当者',
                    'email' => $request->email,
                ];
                // パスワードが入力された時だけ上書きする
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $company->user->update($userData);

                // 2. プラン定価取得
                $plan = Plan::find($request->plan_id);
                $appliedPrice = $request->filled('applied_price') ? $request->applied_price : $plan->base_price;

                // 3. 企業データの更新
                $company->update([
                    'name' => $request->company_name,
                    'agency_id' => $request->agency_id ?: null,
                    'plan_id' => $plan->id,
                    'applied_price' => $appliedPrice,
                    'demo_plan_id' => $request->demo_plan_id ?: null,
                    'demo_expires_at' => $request->filled('demo_plan_id')
                        ? ($request->demo_expires_at ?: null)
                        : null,
                ]);
            });

            return back()->with('success', '「' . $request->company_name . '」の情報を更新しました！');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['system_error' => '更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}