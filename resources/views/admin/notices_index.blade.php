<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><title>お知らせ管理</title></head>
<body>
    <h1>お知らせ管理</h1>
    <a href="{{ route('dashboard') }}">← ダッシュボードへ戻る</a><hr>

    @if (session('success'))
        <p style="color: green;"><b>{{ session('success') }}</b></p>
    @endif

    <h3>1. カテゴリの追加</h3>
    <form action="{{ route('admin.notices.category.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="新カテゴリ名" required>
        <button type="submit">追加</button>
    </form>
    <ul>
        @foreach($categories as $category) <li>{{ $category->name }}</li> @endforeach
    </ul><hr>

    <h3>2. お知らせの投稿</h3>
    <a href="{{ route('admin.notices.create') }}"><button>＋ 新規お知らせを作成</button></a>
    
    <table border="1" cellpadding="8" style="margin-top: 20px;">
        <tr><th>日付</th><th>対象</th><th>カテゴリ</th><th>タイトル</th></tr>
        @foreach($notices as $notice)
            <tr>
                <td>{{ $notice->created_at->format('Y/m/d') }}</td>
                <td>{{ $notice->target_role }}</td>
                <td>{{ $notice->category->name ?? '未分類' }}</td>
                <td>{{ $notice->title }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>