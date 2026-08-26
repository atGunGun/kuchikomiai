<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="mr-2"><img src="/img/top_aicon06.svg" alt="" class="h-10"></span>店舗設定
                </h1>
                <p class="text-gray-800 mt-3">店舗の基本情報や、アンケート画面の案内文をカスタマイズできます。</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-green-600">← ダッシュボードへ戻る</a>
        </div>

        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2 animate-fade-in" role="alert">
                <span class="text-lg font-bold">✓</span> 
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ url('/settings') }}" method="POST" enctype="multipart/form-data" id="settings-form">
            @csrf
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-4">基本情報</h2>
                
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">店舗名 <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $company->name }}" required 
                           class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus-brand block w-full p-3 outline-none transition-all">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">企業・店舗ロゴ</label>
                    @if($company->logo_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="店舗ロゴ" class="h-16 object-contain">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*"
                           class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus-brand block w-full p-2 outline-none">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">テーマカラー</label>
                    <div class="flex items-center gap-4">
                        <input type="color" name="theme_color" value="{{ $company->theme_color ?? '#16a34a' }}" 
                            class="h-12 w-20 cursor-pointer rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-500 font-medium">※アンケート画面のボタンやチェックの色に反映されます。</p>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">住所</label>
                    <input type="text" name="address" value="{{ $company->address ?? '' }}" 
                           class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus-brand block w-full p-3 outline-none transition-all"
                           placeholder="例：東京都渋谷区〇〇 1-2-3">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">
                        Google口コミURL
                    </label>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-3">
                        <p class="text-sm font-bold text-gray-800 mb-2">
                            Googleの「口コミを書く」リンクを設定してください
                        </p>

                        <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                            <li>
                                <a
                                    href="https://business.google.com/"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-blue-600 hover:text-blue-800 font-bold underline"
                                >
                                    Googleビジネスプロフィール
                                </a>
                                を開きます
                            </li>
                            <li>対象の店舗を選択します</li>
                            <li>「クチコミを読む」を選択します</li>
                            <li>「クチコミを増やす」を選択します</li>
                            <li>表示されたリンクをコピーします</li>
                            <li>コピーしたリンクを下の欄に貼り付けます</li>
                        </ol>
                    </div>

                    <input
                        type="url"
                        name="google_map_url"
                        value="{{ $company->google_map_url ?? '' }}"
                        class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus-brand block w-full p-3 outline-none transition-all"
                        placeholder="例：https://g.page/r/..."
                    >

                    <p class="mt-2 text-xs text-gray-500 font-medium">
                        ※アンケート完了後、「このままGoogleに投稿する」を押すと、
                        ここに設定したGoogleの口コミ投稿画面が開きます。
                    </p>
                </div>

                <h2 class="text-lg font-bold text-gray-800 border-b pb-4 pt-4">アンケート画面メッセージ設定</h2>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">ウェルカムメッセージ（アンケート開始時）</label>
                    <input type="hidden" name="welcome_message" id="welcome_message_input" value="{{ $company->welcome_message }}">
                    <div class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                        <div id="welcome-editor" class="bg-white min-h-[120px] text-sm">
                            {!! $company->welcome_message !!}
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 font-medium">※アンケート回答画面の最上部、タイトルの下に表示されます。未入力の場合はアンケート自体の説明文が表示されます。</p>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">完了メッセージ（口コミ生成後ページ）</label>
                    <input type="hidden" name="completion_message" id="completion_message_input" value="{{ $company->completion_message }}">
                    <div class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                        <div id="completion-editor" class="bg-white min-h-[120px] text-sm">
                            {!! $company->completion_message !!}
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 font-medium">※AIが口コミを生成した後の画面（コピーボタン等があるページ）に表示されます。お礼の言葉や、クーポン・特典の案内文などを記載するのに最適です。</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="text-white bg-[#0566F4] hover:bg-green-700 font-extrabold rounded-2xl text-base px-10 py-3 shadow-md  transition-all transform hover:-translate-y-0.5">
                    設定を保存する
                </button>
            </div>
        
        </form>

            {{-- プラン設定 --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6 mt-8">

                <h2 class="text-lg font-bold text-gray-800 border-b pb-4">
                    ご契約プラン
                </h2>

                {{-- 現在のプラン --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <p class="text-sm font-bold text-gray-600 mb-1">現在のプラン</p>

                    @if($company->plan)
                        <p class="text-xl font-extrabold text-green-700">
                            {{ $company->plan->name }}
                        </p>

                        <p class="text-sm text-gray-600 mt-2">
                            月額 {{ number_format($company->plan->base_price) }}円（税込）
                        </p>
                    @else
                        <p class="text-xl font-extrabold text-gray-700">
                            未設定
                        </p>
                    @endif
                </div>

                {{-- プラン一覧 --}}
                <div>
                    <p class="text-sm font-bold text-gray-700 mb-4">
                        プランを変更する
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        @foreach($plans as $plan)

                            <div class="border border-gray-200 rounded-xl p-5">

                                <h3 class="font-bold text-lg text-gray-800">
                                    {{ $plan->name }}
                                </h3>

                                <p class="text-xl font-extrabold text-green-600 mt-2">
                                    {{ number_format($plan->base_price) }}円
                                    <span class="text-sm font-normal text-gray-500">
                                        /月（税込）
                                    </span>
                                </p>

                                @if($plan->description)
                                    <p class="text-sm text-gray-500 mt-3">
                                        {{ $plan->description }}
                                    </p>
                                @endif

                                @if($company->plan_id == $plan->id)

                                    <button
                                        type="button"
                                        disabled
                                        class="mt-4 w-full bg-gray-200 text-gray-500 font-bold rounded-xl py-2 cursor-not-allowed">
                                        現在のプラン
                                    </button>

                                @elseif($plan->base_price == 0)

                                    <form action="{{ route('stripe.free-plan') }}" method="POST">
                                        @csrf

                                        <button
                                            type="submit"
                                            class="mt-4 w-full bg-[#0566F4] hover:bg-green-700 text-white font-bold rounded-xl py-2 transition">
                                            無料プランに変更
                                        </button>
                                    </form>

                                @else

                                    <a
                                        href="{{ route('stripe.checkout', $plan) }}"
                                        class="block text-center mt-4 bg-[#0566F4] hover:bg-green-700 text-white font-bold rounded-xl py-2 transition">
                                        このプランに変更
                                    </a>

                                @endif

                            </div>

                        @endforeach

                    </div>
                </div>

            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        // エディタのツールバー設定（太字、斜体、リスト、リンク、色など最低限使いやすいもの）
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // 飾り文字
            [{ 'color': [] }, { 'background': [] }],          // 文字色・背景色
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],     // 箇条書き
            ['link', 'clean']                                 // リンク挿入・リセット
        ];

        // ウェルカムメッセージエディタの起動
        const welcomeQuill = new Quill('#welcome-editor', {
            modules: { toolbar: toolbarOptions },
            theme: 'snow'
        });

        // 完了メッセージエディタの起動
        const completionQuill = new Quill('#completion-editor', {
            modules: { toolbar: toolbarOptions },
            theme: 'snow'
        });

        // フォーム送信時に、エディタに書かれたHTMLコードをhiddenフィールドに詰め込む処理
        const form = document.getElementById('settings-form');
        form.onsubmit = function() {
            // welcomeエディタのHTMLを取得してセット
            const welcomeInput = document.getElementById('welcome_message_input');
            welcomeInput.value = welcomeQuill.root.innerHTML === '<p><br></p>' ? '' : welcomeQuill.root.innerHTML;

            // completionエディタのHTMLを取得してセット
            const completionInput = document.getElementById('completion_message_input');
            completionInput.value = completionQuill.root.innerHTML === '<p><br></p>' ? '' : completionQuill.root.innerHTML;
        };
    </script>
</x-app-layout>