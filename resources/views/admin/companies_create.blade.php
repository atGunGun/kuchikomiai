<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規企業登録（運営マスタ）</title>
</head>
<body>
    <h1>新規企業（エンド）の登録</h1>
    <a href="{{ route('dashboard') }}">← ダッシュボードへ戻る</a>
    <hr>

    @if (session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <b>{{ session('success') }}</b>
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.companies.store') }}" method="POST">
        @csrf
        
        <h3>1. ログイン情報（エンド企業用）</h3>
        <div>
            <label>企業名（店舗名）:</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" required>
        </div>
        <div>
            <label>ログイン用メールアドレス:</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label>ログイン用パスワード:</label>
            <input type="password" name="password" required minlength="6">
        </div>

        <h3>2. 契約内容の紐付け</h3>
        <div>
            <label>担当代理店:</label>
            <select name="agency_id">
                <option value="">なし（運営の直販）</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>適用プラン:</label>
            <select name="plan_id" required>
                <option value="">選択してください</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} (定価: {{ number_format($plan->base_price) }}円)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>実際の請求金額（円）:</label>
            <input type="number" name="applied_price" value="{{ old('applied_price') }}" min="0" placeholder="※空欄なら定価を適用">
            <br>
            <small>代理店特別価格など、定価と違う金額で請求する場合のみ入力してください。</small>
        </div>

        <br>
        <button type="submit">この内容で企業を登録する</button>
    </form>
</body>
</html>