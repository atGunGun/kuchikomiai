<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\NoticeCategory;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class AdminNoticeController extends Controller
{
    // お知らせ一覧とカテゴリ登録画面
    public function index()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        
        $notices = Notice::with('category')->latest()->get();
        $categories = NoticeCategory::all();
        
        return view('admin.notices_index', compact('notices', 'categories'));
    }

    // カテゴリの保存
    public function storeCategory(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        NoticeCategory::create($request->all());
        return back()->with('success', 'カテゴリを追加しました。');
    }

    // 記事投稿画面
    public function create()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $categories = NoticeCategory::all();
        return view('admin.notices_create', compact('categories'));
    }

    // 記事の保存
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required', // WYSIWYGのHTMLが入ります
            'target_role' => 'required|in:all,agency,company',
            'notice_category_id' => 'required|exists:notice_categories,id',
        ]);

        Notice::create($request->all());
        return redirect()->route('admin.notices.index')->with('success', 'お知らせを投稿しました！');
    }

    // --- 既存のメソッドの下に追記 ---

    // 編集画面の表示
    public function edit(Notice $notice)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $categories = NoticeCategory::all();
        return view('admin.notices_edit', compact('notice', 'categories'));
    }

    // データの更新処理
    public function update(Request $request, Notice $notice)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'target_role' => 'required|in:all,agency,company',
            'notice_category_id' => 'required|exists:notice_categories,id',
        ]);

        $notice->update($request->all());

        return redirect()->route('admin.notices.index')->with('success', 'お知らせを更新しました！');
    }
    // 設定画面の表示（既存のindexに混ぜてもOKですが、分ける場合）
    public function showSettings()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $noticePerPage = GlobalSetting::where('key', 'notice_per_page')->value('value') ?: 10;
        return view('admin.settings', compact('noticePerPage'));
    }

    // 設定の保存
    public function updateSettings(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        
        GlobalSetting::updateOrCreate(
            ['key' => 'notice_per_page'],
            ['value' => $request->notice_per_page]
        );

        return back()->with('success', '設定を更新しました。');
    }
}