<div style="max-width: 500px; margin: 50px auto; font-family: sans-serif;">
    @auth
        <div style="margin-bottom: 20px;">
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: #666; font-size: 14px; background: #eee; padding: 5px 10px; border-radius: 4px;">🏠 ダッシュボードに戻る</a>
        </div>
    @endauth
    <h2>{{ $company->name ?? 'お店' }} の口コミを作成しました！</h2>
    <textarea id="aiResult" style="width:100%; height:150px; padding:10px; font-size: 15px;">{{ $aiText }}</textarea>
    
    <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
        <a href="/" style="text-decoration: none; color: #4285F4;">← 戻る</a>
        <button onclick="copyToClipboard(this)" style="padding: 10px 20px; background: #34A853; color: white; border: none; cursor: pointer; font-size: 14px; border-radius: 4px;">📝 コピーする</button>
    </div>
    
    <script>
    function copyToClipboard(button) {
        var copyText = document.getElementById("aiResult");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        var originalText = button.innerHTML;
        var originalColor = button.style.background;
        
        button.innerHTML = "✅ コピーしました！";
        button.style.background = "#188038";
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.style.background = originalColor;
            window.getSelection().removeAllRanges();
        }, 3000);
    }
    </script>
</div>