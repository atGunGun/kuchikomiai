<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class PublicNoticeController extends Controller
{
    // お知らせ一覧ページ
    public function index(Request $request)
    {
        // 1. 管理者が設定した「デフォルト件数」を取得（なければ10）
        $defaultPerPage = \App\Models\GlobalSetting::where('key', 'notice_per_page')->value('value') ?: 10;
        
        // 2. ユーザーが画面で選んだ件数があればそれを使う。なければデフォルト。
        $perPage = $request->query('per_page', $defaultPerPage);

        $role = auth()->check() ? auth()->user()->role : 'all';
        $notices = \App\Models\Notice::with('category')
                    ->whereIn('target_role', ['all', $role])
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString(); // これを書くことで、ページをめくっても「表示件数」の設定が維持されます

        return view('notices.index', compact('notices', 'perPage'));
    }

    // お知らせ詳細ページ
    public function show(Notice $notice)
    {
        // 権限チェック（一応）
        $role = auth()->check() ? auth()->user()->role : 'all';
        if ($notice->target_role !== 'all' && $notice->target_role !== $role) {
            abort(403);
        }

        return view('notices.show', compact('notice'));
    }
    // app/Http/Controllers/PublicNoticeController.php

    public function top(Request $request)
    {
        // 管理者のデフォルト設定を取得
        $defaultPerPage = \App\Models\GlobalSetting::where('key', 'notice_per_page')->value('value') ?: 5;
        $perPage = $request->query('per_page', $defaultPerPage);

        $role = auth()->check() ? auth()->user()->role : 'all';
        
        // ページネーションを有効にして取得
        $notices = \App\Models\Notice::with('category')
                    ->whereIn('target_role', ['all', $role])
                    ->latest()
                    ->paginate($perPage)
                    ->withQueryString();

        return view('welcome', compact('notices', 'perPage'));
    }
}