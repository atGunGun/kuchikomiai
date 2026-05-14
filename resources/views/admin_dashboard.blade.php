<div style="padding: 30px; font-family: sans-serif; background: #fdf2f2; min-height: 100vh;">
    <h1 style="color: #c62828;">👑 運営（システム管理者）</h1>

    <div style="text-align: right; margin-bottom: 20px;">
        <a href="{{ route('admin.settings.global') }}" style="text-decoration: none; background: #455a64; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold;">
            ⚙️ システム設定（表示件数など）
        </a>
        <a href="{{ route('admin.plans.index') }}" style="text-decoration: none; background: #e65100; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; margin-right: 10px;">
            📋 プラン管理（上限数の設定など）
        </a>
    </div>
    

    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0; color: #333;">📊 全体統計</h2>
        <p style="font-size: 24px; margin: 10px 0;">登録企業総数: <strong>{{ $companyCount }}</strong> 件</p>
    </div>
    

    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 style="margin:0; color: #333;">📢 お知らせ管理</h2>
            <div>
                <a href="{{ route('admin.notices.index') }}" style="text-decoration: none; background: #757575; color: white; padding: 8px 15px; border-radius: 5px; font-size: 14px; margin-right: 5px;">カテゴリ・一覧管理</a>
                <a href="{{ route('admin.notices.create') }}" style="text-decoration: none; background: #2e7d32; color: white; padding: 8px 15px; border-radius: 5px; font-size: 14px;">＋ 新規投稿</a>
            </div>
        </div>

        <table border="1" style="width:100%; border-collapse: collapse; background: white; font-size: 14px;">
            <tr style="background: #f5f5f5;">
                <th style="padding: 8px;">日付</th>
                <th>カテゴリ</th>
                <th>タイトル</th>
                <th>対象</th>
                <th>操作</th>
            </tr>
            @php
                // 直近5件を取得
                $recentNotices = \App\Models\Notice::with('category')->latest()->take(5)->get();
            @endphp
            @forelse($recentNotices as $notice)
            <tr style="text-align: center;">
                <td style="padding: 8px;">{{ $notice->created_at->format('m/d') }}</td>
                <td><span style="background: #eee; padding: 2px 6px; border-radius: 4px;">{{ $notice->category->name ?? '-' }}</span></td>
                <td style="text-align: left; padding: 8px;">{{ $notice->title }}</td>
                <td>{{ $notice->target_role }}</td>
                <td>
                    <a href="{{ route('admin.notices.edit', $notice->id) }}" style="color: #1976d2;">編集</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 10px; color: #777;">まだ投稿されたお知らせはありません。</td>
            </tr>
            @endforelse
        </table>
    </div>

    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0; color: #333;">🏢 企業一覧</h2>
        <table border="1" style="width:100%; border-collapse: collapse; background: white;">
            <tr style="background: #eee;">
                <th style="padding: 10px;">企業名</th>
                <th>担当代理店</th>
                <th>操作</th>
            </tr>
            @foreach($companies as $company)
            <tr style="text-align: center;">
                <td style="padding: 10px; text-align: left;">{{ $company->name }}</td>
                <td>{{ $company->agency->name ?? '直販' }}</td>
                <td>
                    <a href="{{ route('admin.companies.edit', $company->id) }}" style="color: #1976d2;">編集</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <form method="POST" action="/logout" style="margin-top:20px;">
        @csrf
        <button style="cursor:pointer; padding: 5px 15px;">ログアウト</button>
    </form>
</div>