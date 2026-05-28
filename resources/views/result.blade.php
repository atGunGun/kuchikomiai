<x-app-layout>
    <style>
        :root { 
            --brand-color: {{ $company->theme_color ?? '#16a34a' }};
            /* 完了メッセージの背景用に透明度を入れた色（約8%） */
            --brand-color-light: {{ ($company->theme_color ?? '#16a34a') . '15' }}; 
        }
        
        .bg-brand { background-color: var(--brand-color) !important; }
        .text-brand { color: var(--brand-color) !important; }
        .border-brand { border-color: var(--brand-color) !important; }
        
        /* 完了メッセージ用の専用スタイル */
        .msg-box-brand {
            background-color: var(--brand-color-light) !important;
            border-color: var(--brand-color) !important;
        }

        /* メインボタンのホバー */
        .btn-brand-hover:hover {
            filter: brightness(0.9) !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* 文字やリンクに対する装飾 */
        .prose-brand a { color: var(--brand-color) !important; text-decoration: underline; }
        .prose-brand strong { color: var(--brand-color) !important; }
    </style>
    
    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8 flex justify-center items-start">
        <div class="max-w-2xl w-full bg-white shadow-sm border border-gray-100 rounded-3xl p-6 sm:p-10 mt-6 relative overflow-hidden">
            
            {{-- 上部の装飾バーをテーマカラーに --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-brand"></div>

            @auth
                <div class="mb-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors bg-gray-100 px-4 py-2 rounded-lg">
                        ダッシュボードに戻る
                    </a>
                </div>
            @endauth

            {{-- ★追加：完了画面の企業ロゴ --}}
            @if($company->logo_path)
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }} ロゴ" class="max-h-20 object-contain drop-shadow-sm">
                </div>
            @endif

            {{-- 完了メッセージ（設定されていればテーマカラーの枠で表示） --}}
            @if(!empty($company->completion_message))
                <div class="mb-8 p-5 msg-box-brand border border-opacity-30 rounded-2xl text-gray-700 text-sm leading-relaxed prose prose-brand max-w-none shadow-sm">
                    {!! $company->completion_message !!}
                </div>
            @endif

            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-6 text-center tracking-tight">あなたのための口コミが完成しました</h2>

            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 mb-8 relative">
                <p id="generated-text" class="text-gray-700 text-sm sm:text-base leading-relaxed whitespace-pre-wrap break-words">{{ $aiText }}</p>
            </div>

            <div class="space-y-4">
                {{-- ① このまま投稿するメインボタン（テーマカラーを適用） --}}
                @if(!empty($company->google_map_url))
                    <button onclick="copyAndRedirect(this)" class="w-full text-white bg-brand btn-brand-hover font-extrabold rounded-2xl text-lg px-5 py-4 shadow-lg transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span class="text-xl"></span> <span id="btn-text">このままGoogleに投稿する</span>
                    </button>
                    <p class="text-xs text-center text-gray-500 font-medium">※文章がコピーされ、Googleマップが開きます。<br>入力欄に「ペースト（貼り付け）」して投稿してください。</p>
                @endif

                {{-- ② 自分で編集したい人向けのコピーボタン --}}
                <button onclick="onlyCopy(this)" class="w-full text-gray-700 bg-white border-2 border-gray-200 hover:bg-gray-50 font-bold rounded-2xl text-base px-5 py-4 transition-all flex items-center justify-center gap-2 mt-4">
                    自分で編集するため文章だけコピーする
                </button>
            </div>
            
            <div class="mt-8 text-center">
                <a href="{{ route('review.show', $company->token) }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors">
                    ← はじめからやり直す
                </a>
            </div>
        </div>
    </div>

    <script>
        const reviewId = "{{ $review->id ?? '' }}";
        const trackUrl = reviewId ? "/review/" + reviewId + "/track" : "";
        const csrfToken = "{{ csrf_token() }}";

        // ★ どんな環境でも絶対にコピーを成功させるための最強の関数
        async function copyToClipboardFallback(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            } else {
                let textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                return new Promise((resolve, reject) => {
                    let success = document.execCommand('copy');
                    textArea.remove();
                    if (success) {
                        resolve();
                    } else {
                        reject(new Error("コピーに失敗しました"));
                    }
                });
            }
        }

        // ① そのままGoogleへ投稿する処理
        async function copyAndRedirect(button) {
            const originalHtml = button.innerHTML;
            const textSpan = button.querySelector('#btn-text');
            
            try {
                const text = document.getElementById('generated-text').innerText;
                await copyToClipboardFallback(text);

                // コピー成功時の見た目変更（テーマカラーのまま少し暗くする）
                if (textSpan) {
                    textSpan.innerText = "✅ コピー完了！移動します...";
                } else {
                    button.innerHTML = "<span>✅ コピー完了！移動します...</span>";
                }
                button.style.filter = "brightness(0.85)";

                // 裏側で「そのまま投稿（direct_post）」をカウント
                if (trackUrl) {
                    await fetch(trackUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ action: 'direct_post' })
                    }).catch(e => console.log(e));
                }

                // Googleマップへ遷移
                setTimeout(() => {
                    window.location.href = "{{ $company->google_map_url ?? '' }}";
                }, 800);

            } catch (error) {
                alert('コピーに失敗しました。文章を直接選択してコピーしてください。');
                button.innerHTML = originalHtml;
                button.style.filter = "none";
            }
        }

        // ② コピーだけする処理
        async function onlyCopy(button) {
            const originalHtml = button.innerHTML;
            try {
                const text = document.getElementById('generated-text').innerText;
                await copyToClipboardFallback(text);

                button.innerHTML = "✅ コピーしました！";
                button.classList.add('bg-gray-100');
                button.style.borderColor = "var(--brand-color)";
                button.style.color = "var(--brand-color)";

                // 裏側で「単なるコピー（copy）」をカウント
                if (trackUrl) {
                    await fetch(trackUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ action: 'copy' })
                    }).catch(e => console.log(e));
                }

                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('bg-gray-100');
                    button.style.borderColor = "";
                    button.style.color = "";
                }, 3000);

            } catch (error) {
                alert('コピーに失敗しました。手動でコピーしてください。');
            }
        }
    </script>
</x-app-layout>