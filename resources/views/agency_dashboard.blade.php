<div style="padding: 30px; font-family: sans-serif; background: #f0f7ff; min-height: 100vh;">
    <h1 style="color: #1565c0;">🏢 代理店ダッシュボード</h1>
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">📈 担当状況</h2>
        <p style="font-size: 24px;">担当企業数: <strong>{{ $companyCount }}</strong> 件</p>
    </div>

    <h3>📋 担当企業リスト</h3>
    <ul>
        @foreach($myCompanies as $company)
            <li style="font-size: 18px; margin-bottom: 10px;">{{ $company->name }}（{{ $company->address }}）</li>
        @endforeach
    </ul>
    <form method="POST" action="/logout" style="margin-top:20px;">@csrf<button>ログアウト</button></form>
</div>