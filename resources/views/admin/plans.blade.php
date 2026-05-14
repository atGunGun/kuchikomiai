<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プラン管理マスタ</title>
</head>
<body>
    <h1>プラン管理マスタ</h1>
    <a href="{{ route('dashboard') }}">← ダッシュボードへ戻る</a>
    <hr>

    @if (session('success'))
        <p style="color: green;"><b>{{ session('success') }}</b></p>
    @endif

    <h2>新規プラン登録</h2>
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        <div>
            <label>プラン名:</label>
            <input type="text" name="name" required placeholder="例: スタンダードプラン">
        </div>
        <div>
            <label>基本定価 (円):</label>
            <input type="number" name="base_price" required>
        </div>
        <div>
            <label>アンケート作成上限数:</label>
            <input type="number" name="max_surveys" min="1"> <small>※空欄の場合は「無制限」</small>
        </div>
        <div>
            <label>AI生成上限回数:</label>
            <input type="number" name="max_generations" min="1"> <small>※空欄の場合は「無制限」</small>
        </div>

        <div>
            <label>説明 (任意):</label>
            <input type="text" name="description" placeholder="機能制限など">
        </div>
        <button type="submit">登録する</button>
    </form>

    <hr>

    <h2>登録済みプラン一覧</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>プラン名</th>
            <th>基本定価</th>
            <th>アンケート上限数</th>
            <th>生成上限回数</th>
            <th>説明</th>
            <th>操作</th>
        </tr>
        @forelse ($plans as $plan)
            <tr>
                <td>{{ $plan->id }}</td>
                <td>{{ $plan->name }}</td>
                <td>{{ number_format($plan->base_price) }} 円</td>
                <td>{{ is_null($plan->max_surveys) ? '無制限 (∞)' : $plan->max_surveys . ' 個' }}</td>
                <td>{{ is_null($plan->max_generations) ? '無制限 (∞)' : $plan->max_generations . ' 回' }}</td>
                <td>{{ $plan->description }}</td>
                <td>
                    <a href="{{ route('admin.plans.edit', $plan->id) }}" style="margin-right: 10px;">編集</a>
                    
                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('本当に削除しますか？')">削除</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">登録されているプランはありません。</td>
            </tr>
        @endforelse
    </table>
</body>
</html>