<x-app-layout>
    <div class="space-y-6">
        

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <img src="/img/top_aicon03.svg" alt="" class="h-10"></span>  アンケート管理
                </h1>
                <p class="text-gray-800 mt-2">作成したアンケートの管理と、店頭でお客様に回答してもらうアンケートの切り替えができます。</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-green-600">← ダッシュボードへ戻る</a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
            
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-800">登録済みアンケート一覧</h2>
                <a href="{{ route('surveys.create') }}" class="flex items-center justify-center text-white bg-[#0566F4] hover:bg-green-700 font-bold rounded-xl text-sm px-6 py-2.5 transition-colors shadow-sm">
                    ＋ 新規作成
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-white border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">タイトル</th>
                            <th scope="col" class="px-6 py-4 text-center font-bold">状態</th>
                            <th scope="col" class="px-6 py-4 text-center font-bold">作成日</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($surveys as $survey)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-5 font-bold text-gray-900 text-base">
                                {{ $survey->title }}
                            </td>
                            
                            <td class="px-6 py-5 text-center">
                                @if($company->selected_survey_id === $survey->id)
                                    <span class="inline-flex items-center gap-1 bg-[#08866D] text-[#fff] text-xs font-bold px-3 py-1.5 rounded-full border border-[#08866D] shadow-sm">
                                         使用中 
                                    </span>
                                @else
                                    <form action="{{ route('surveys.select', $survey->id) }}" method="POST">
                                        @csrf
                                        <button class="text-gray-600 bg-white hover:bg-gray-50 border border-gray-300 font-medium rounded-lg text-xs px-4 py-1.5 transition-colors shadow-sm">
                                            これを使用する
                                        </button>
                                    </form>
                                @endif
                            </td>
                            
                            <td class="px-6 py-5 text-center text-gray-500 font-medium">
                                {{ $survey->created_at->format('Y/m/d') }}
                            </td>
                            
                            <td class="px-6 py-5 text-right space-x-3">
                                <a href="{{ route('surveys.edit', $survey->id) }}" class="text-green-600 hover:text-green-800 font-bold px-2 py-1 transition-colors">編集</a>
                                <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('本当に削除しますか？')" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 transition-colors">削除</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-lg font-medium mb-2">まだアンケートがありません</p>
                                <p class="text-sm">右上の「＋ 新規作成」ボタンから、最初のアンケートを作りましょう！</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</x-app-layout>