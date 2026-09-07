<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>企業情報編集（運営マスタ）</title>
</head>
<body>
    <h1>「{{ $company->name }}」の情報を編集</h1>
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

    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h3>1. ログイン情報（エンド企業用）</h3>
        <div>
            <label>企業名（店舗名）:</label>
            <input type="text" name="company_name" value="{{ old('company_name', $company->name) }}" required>
        </div>
        <div>
            <label>ログイン用メールアドレス:</label>
            <input type="email" name="email" value="{{ old('email', $company->user?->email) }}" required>
        </div>
        <div>
            <label>ログイン用パスワード (※変更する場合のみ入力):</label>
            <input type="password" name="password" minlength="6">
        </div>

        <h3>2. 契約内容の紐付け</h3>
        <div>
            <label>担当代理店:</label>
            <select name="agency_id">
                <option value="">なし（運営の直販）</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ old('agency_id', $company->agency_id) == $agency->id ? 'selected' : '' }}>
                        {{ $agency->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>適用プラン:</label>
            <select name="plan_id" required>
                <option value="">選択してください</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" {{ old('plan_id', $company->plan_id) == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} (定価: {{ number_format($plan->base_price) }}円)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>実際の請求金額（円）:</label>
            <input type="number" name="applied_price" value="{{ old('applied_price', $company->applied_price) }}" min="0">
        </div>

        <hr>

        <h3>3. デモプラン設定</h3>

        <div>
            <label>デモプラン:</label>
            <select name="demo_plan_id">
                <option value="">なし（実契約プランを使用）</option>

                @foreach ($demoPlans as $demoPlan)
                    <option
                        value="{{ $demoPlan->id }}"
                        {{ old('demo_plan_id', $company->demo_plan_id) == $demoPlan->id ? 'selected' : '' }}
                    >
                        {{ $demoPlan->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label>デモ終了日時:</label>
            <input
                type="datetime-local"
                name="demo_expires_at"
                value="{{ old('demo_expires_at', $company->demo_expires_at?->format('Y-m-d\TH:i')) }}"
            >
            <small>空欄の場合は無期限です。</small>
        </div>

        <p>
            ※ デモプランを設定しても実契約プランや請求情報は変更されません。
        </p>

        <br>
        <button type="submit">情報を更新する</button>
    </form>
</body>
</html>