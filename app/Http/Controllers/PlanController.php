<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // プラン一覧と登録フォームの表示
    public function index()
    {
        // 運営（admin）以外がアクセスできないようにガード
        if (auth()->user()->role !== 'admin') {
            abort(403, '権限がありません');
        }

        $plans = Plan::all();
        return view('admin.plans', compact('plans'));
    }

    // プランの保存処理
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|integer|min:0',
        ]);

        Plan::create($request->all());

        return back()->with('success', '新しいプランを登録しました！');
    }

    // プランの削除処理（必要に応じて）
    public function destroy(Plan $plan)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        
        $plan->delete();
        return back()->with('success', 'プランを削除しました。');
    }

    public function edit(\App\Models\Plan $plan)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        return view('admin.plans_edit', compact('plan'));
    }

    public function update(Request $request, \App\Models\Plan $plan)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|integer',
            'max_surveys' => 'nullable|integer|min:1', // ★ required を nullable に
            'max_generations' => 'nullable|integer|min:1', // ★ required を nullable に
            'description' => 'nullable|string',
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.plans.index')->with('success', 'プランを更新しました！');
    }
}