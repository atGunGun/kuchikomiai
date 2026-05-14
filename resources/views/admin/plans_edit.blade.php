<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プラン編集</title>
</head>
<body>
    <h1>プラン編集</h1>
    <a href="{{ route('admin.plans.index') }}">← プラン一覧に戻る</a>
    <hr>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px;">
            <label>プラン名:</label><br>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required style="width: 300px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>基本定価 (円):</label><br>
            <input type="number" name="base_price" value="{{ old('base_price', $plan->base_price) }}" required>
        </div>
        <div style="margin-bottom: 15px;">
            <label>アンケート作成上限数:</label><br>
            <input type="number" name="max_surveys" value="{{ old('max_surveys', $plan->max_surveys) }}" min="1">
            <br><small>※空欄で保存した場合は「無制限 (∞)」になります。</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label>AI生成上限回数:</label><br>
            <input type="number" name="max_generations" value="{{ old('max_generations', $plan->max_generations) }}" min="1">
            <br><small>※空欄で保存した場合は「無制限 (∞)」になります。</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label>説明 (任意):</label><br>
            <textarea name="description" style="width: 300px; height: 80px;">{{ old('description', $plan->description) }}</textarea>
        </div>

        <button type="submit" style="padding: 10px 20px; background-color: #1976d2; color: white; border: none; border-radius: 5px; cursor: pointer;">
            更新する
        </button>
    </form>

    <hr>
    <p style="font-size: 0.8em; color: #666;">※プラン内容を変更しても、すでに登録済みの企業の適用価格などは自動では変わりません。</p>
</body>
</html>