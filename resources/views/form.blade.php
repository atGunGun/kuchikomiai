<x-app-layout>
    <style>
        :root {
            /* 企業データに色がなければデフォルトの緑色（#16a34a）を使用 */
            --brand-color: {{ $company->theme_color ?? '#16a34a' }};
        }

        /* 背景と文字色 */
        .bg-brand { background-color: var(--brand-color) !important; }
        .text-brand { color: var(--brand-color) !important; }

        /* フォームのフォーカス状態 */
        .focus-brand:focus {
            border-color: var(--brand-color) !important;
            box-shadow: 0 0 0 1px var(--brand-color) !important;
        }

        /* 枠線のホバー */
        .hover-border-brand:hover {
            border-color: var(--brand-color) !important;
        }

        /* 選択肢ボタン（通常時とホバー時） */
        .option-card:hover {
            border-color: var(--brand-color) !important;
            background-color: #f9fafb !important;
        }

        /* 選択肢ボタン（チェックされた時） */
        .peer:checked + .option-card {
            background-color: var(--brand-color) !important;
            border-color: var(--brand-color) !important;
            color: #fff !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        /* チェックされた時だけチェックマーク（SVG）を表示する */
        .peer:checked + .option-card .check-icon {
            display: block !important;
        }

        /* 送信ボタンのホバー効果（元の色を少し暗くする） */
        .btn-submit:hover {
            filter: brightness(0.9) !important;
        }
    </style>

    <div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8 flex justify-center items-start">
        <div class="max-w-2xl w-full bg-white shadow-sm border border-gray-100 rounded-3xl p-6 sm:p-10 mt-6 relative overflow-hidden">
            
            {{-- 上部の装飾バー --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-brand"></div>

            {{-- エラーメッセージ --}}
            @if(isset($error) || session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 flex items-center gap-2" role="alert">
                    <span class="text-xl font-bold">⚠️</span> 
                    <span>{{ $error ?? session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('review.generate') }}" method="POST" id="survey-form">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id ?? '' }}">
                
                {{-- 所要時間を送るための隠しフィールド --}}
                <input type="hidden" name="duration_seconds" id="duration_seconds" value="0">

                @if($company->selectedSurvey)
                    
                    {{-- 企業ロゴの表示エリア --}}
                    @if($company->logo_path)
                        <div class="flex justify-center mb-8 review_logo">
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }} ロゴ" class="max-h-20 object-contain drop-shadow-sm">
                        </div>
                    @endif

                    {{-- タイトルエリア --}}
                    <div class="mb-10 text-center border-b border-gray-100 pb-8">
                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $company->selectedSurvey->title }}</h2>
                        
                        <div class="mt-3 text-gray-500 text-sm sm:text-base leading-relaxed text-left max-w-xl mx-auto prose prose-green">
                            @if($company->welcome_message)
                                {!! $company->welcome_message !!}
                            @elseif($company->selectedSurvey->description)
                                {{ $company->selectedSurvey->description }}
                            @endif
                        </div>
                    </div>

                    {{-- 設問ループ --}}
                    <div class="space-y-8">
                        @foreach($company->selectedSurvey->questions as $index => $question)
                            <div class="p-6 bg-white border border-gray-100 rounded-2xl shadow-sm hover-border-brand transition-colors">
                                
                                <label class="block mb-4 text-base font-bold text-gray-900 flex items-center flex-wrap gap-2">
                                    <span class="text-brand text-lg">Q{{ $index + 1 }}.</span> 
                                    <span>{{ $question->question_text }}</span>
                                    
                                    @if($question->is_required)
                                        <span class="bg-red-50 text-red-600 border border-red-200 text-xs font-bold px-2 py-0.5 rounded-md ml-auto">必須</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-0.5 rounded-md ml-auto">任意</span>
                                    @endif
                                </label>

                                {{-- 短いテキスト --}}
                                @if($question->type === 'text')
                                    <input type="text" name="answers[{{ $question->id }}]" value="{{ old('answers.'.$question->id) }}" 
                                           class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-4 outline-none transition-all focus-brand" 
                                           placeholder="ご記入ください" {{ $question->is_required ? 'required' : '' }}>

                                {{-- 長いテキスト --}}
                                @elseif($question->type === 'textarea')
                                    <textarea name="answers[{{ $question->id }}]" rows="3" 
                                              class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl block w-full p-4 outline-none transition-all focus-brand" 
                                              placeholder="ご自由にご記入ください" {{ $question->is_required ? 'required' : '' }}>{{ old('answers.'.$question->id) }}</textarea>

                                {{-- チェックボックス --}}
                                @elseif($question->type === 'checkbox')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                        @php 
                                            $oldValues = old('answers.'.$question->id, []); 
                                            if (!is_array($oldValues)) $oldValues = [$oldValues];
                                        @endphp
                                        @if(is_array($question->options))
                                            @foreach($question->options as $optIndex => $option)
                                                <label class="relative cursor-pointer group block">
                                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" 
                                                           {{ in_array($option, $oldValues) ? 'checked' : '' }}
                                                           class="sr-only peer">
                                                    
                                                    <div class="relative px-6 py-4 bg-white border-4 border-gray-100 rounded-2xl text-center font-bold text-gray-700 transition-all duration-200 option-card flex items-center justify-center gap-2">
                                                        
                                                        {{-- チェックされた時だけ表示されるアイコン（CSSで制御） --}}
                                                        <div class="hidden check-icon w-6 h-6 shrink-0 bg-white p-1 rounded-full text-brand shadow-inner">
                                                            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                        </div>

                                                        <span>{{ $option }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>

                                {{-- ラジオボタン --}}
                                @elseif($question->type === 'radio')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                        @php $oldValue = old('answers.'.$question->id); @endphp
                                        @if(is_array($question->options))
                                            @foreach($question->options as $optIndex => $option)
                                                <label class="relative cursor-pointer group block">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" 
                                                           {{ $oldValue === $option ? 'checked' : '' }} {{ $question->is_required ? 'required' : '' }}
                                                           class="sr-only peer">
                                                    
                                                    <div class="relative px-6 py-4 bg-white border-4 border-gray-100 rounded-2xl text-center font-bold text-gray-700 transition-all duration-200 option-card flex items-center justify-center gap-2">
                                                        
                                                        {{-- チェックされた時だけ表示されるアイコン（CSSで制御） --}}
                                                        <div class="hidden check-icon w-6 h-6 shrink-0 bg-white p-1 rounded-full text-brand shadow-inner">
                                                            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                        </div>

                                                        <span>{{ $option }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @else
                    {{-- アンケート未設定時 --}}
                    <div class="p-4 mb-4 text-sm text-yellow-800 rounded-xl bg-yellow-50 border border-yellow-200 text-center font-medium" role="alert">
                        現在、回答可能なアンケートが設定されていません。
                    </div>
                @endif
                
                {{-- 送信ボタン --}}
                <div class="mt-12">
                    <button type="submit" class="w-full text-white bg-brand btn-submit font-extrabold rounded-2xl text-lg px-5 py-4 shadow-lg transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                        <span class="text-2xl">✨</span> AIで口コミを作成する
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4 font-medium">※AIの生成には10〜15秒ほどかかる場合があります</p>
                </div>
            </form>
        </div>
    </div>

    {{-- 所要時間の計測スクリプト --}}
    <script>
        const startTime = Date.now();

        document.getElementById('survey-form').addEventListener('submit', function() {
            const durationSeconds = Math.floor((Date.now() - startTime) / 1000);
            document.getElementById('duration_seconds').value = durationSeconds;
        });
    </script>
</x-app-layout>