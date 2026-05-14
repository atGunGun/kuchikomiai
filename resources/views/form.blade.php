<x-app-layout>
    {{-- ※もし app-layout を使っていない公開専用ページなら、<html>や<head>タグで囲んでください --}}
    
    <div class="min-h-screen bg-gray-100 py-10 px-4 sm:px-6 lg:px-8 flex justify-center items-start">
        <div class="max-w-2xl w-full bg-white shadow-xl rounded-2xl p-6 sm:p-10 mt-6">
            
            {{-- エラーメッセージ（Flowbiteのアラートデザイン） --}}
            @if(isset($error) || session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
                    <span class="font-bold">エラー：</span> {{ $error ?? session('error') }}
                </div>
            @endif

            <form action="{{ route('review.generate') }}" method="POST">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id ?? '' }}">

                @if($company->selectedSurvey)
                    {{-- タイトルエリア --}}
                    <div class="mb-8 text-center border-b pb-6">
                        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $company->selectedSurvey->title }}</h2>
                        @if($company->selectedSurvey->description)
                            <p class="mt-3 text-gray-500 text-sm sm:text-base">{{ $company->selectedSurvey->description }}</p>
                        @endif
                    </div>

                    {{-- 設問ループ --}}
                    @foreach($company->selectedSurvey->questions as $index => $question)
                        <div class="mb-6 p-5 bg-gray-50 border border-gray-200 rounded-xl hover:shadow-md transition-shadow duration-200">
                            
                            <label class="block mb-3 text-base font-bold text-gray-900">
                                <span class="text-blue-600 mr-1">Q{{ $index + 1 }}.</span> {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold me-2 px-2.5 py-0.5 rounded ml-2">必須</span>
                                @else
                                    <span class="bg-gray-200 text-gray-600 text-xs font-semibold me-2 px-2.5 py-0.5 rounded ml-2">任意</span>
                                @endif
                            </label>

                            {{-- 短いテキスト --}}
                            @if($question->type === 'text')
                                <input type="text" name="answers[{{ $question->id }}]" value="{{ old('answers.'.$question->id) }}" 
                                       class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 shadow-sm" 
                                       placeholder="ご記入ください" {{ $question->is_required ? 'required' : '' }}>

                            {{-- 長いテキスト --}}
                            @elseif($question->type === 'textarea')
                                <textarea name="answers[{{ $question->id }}]" rows="3" 
                                          class="bg-white block p-3 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                                          placeholder="ご自由にご記入ください" {{ $question->is_required ? 'required' : '' }}>{{ old('answers.'.$question->id) }}</textarea>

                            {{-- チェックボックス（複数選択） --}}
                            @elseif($question->type === 'checkbox')
                                <div class="flex flex-wrap gap-4 mt-3">
                                    @php 
                                        $oldValues = old('answers.'.$question->id, []); 
                                        if (!is_array($oldValues)) $oldValues = [$oldValues];
                                    @endphp
                                    @if(is_array($question->options))
                                        @foreach($question->options as $optIndex => $option)
                                            <div class="flex items-center">
                                                <input id="check-{{ $question->id }}-{{ $optIndex }}" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" 
                                                       {{ in_array($option, $oldValues) ? 'checked' : '' }}
                                                       class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer shadow-sm">
                                                <label for="check-{{ $question->id }}-{{ $optIndex }}" class="ms-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-colors">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            {{-- ラジオボタン（1つだけ選択） --}}
                            @elseif($question->type === 'radio')
                                <div class="flex flex-wrap gap-4 mt-3">
                                    @php $oldValue = old('answers.'.$question->id); @endphp
                                    @if(is_array($question->options))
                                        @foreach($question->options as $optIndex => $option)
                                            <div class="flex items-center">
                                                <input id="radio-{{ $question->id }}-{{ $optIndex }}" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" 
                                                       {{ $oldValue === $option ? 'checked' : '' }} {{ $question->is_required ? 'required' : '' }}
                                                       class="w-5 h-5 text-blue-600 bg-white border-gray-300 focus:ring-blue-500 focus:ring-2 cursor-pointer shadow-sm">
                                                <label for="radio-{{ $question->id }}-{{ $optIndex }}" class="ms-2 text-sm font-medium text-gray-700 cursor-pointer hover:text-blue-600 transition-colors">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                        </div>
                    @endforeach

                @else
                    {{-- アンケート未設定時 --}}
                    <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-200" role="alert">
                        現在、回答可能なアンケートが設定されていません。
                    </div>
                @endif
                
                {{-- 送信ボタン（目を引くグラデーション） --}}
                <div class="mt-8">
                    <button type="submit" class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 shadow-lg shadow-blue-500/50 font-bold rounded-xl text-lg px-5 py-4 text-center w-full transition-all duration-200 transform hover:-translate-y-1">
                        ✨ AIで口コミを作成する
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-3">※生成には10〜15秒ほどかかる場合があります</p>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>