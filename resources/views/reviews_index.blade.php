<x-app-layout>
    <div class="space-y-6">
        
        <div class="flex items-center justify-between border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">📝</span> 口コミ生成履歴
                </h1>
                <p class="text-gray-500 mt-1">これまでにAIが生成したすべての口コミを確認できます。</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-green-600">← ダッシュボードへ戻る</a>
        </div>

        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">生成日時</th>
                            <th scope="col" class="px-6 py-4 font-bold">AI生成テキスト</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">コピー</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">Google遷移</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap text-gray-500 font-medium">
                                {{ $review->created_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-5 text-gray-800">
                                {{ $review->generated_text }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($review->is_copied)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full">済</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($review->is_redirected)
                                    <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2.5 py-1 rounded-full">済</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">
                                まだ生成された口コミ履歴はありません。
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $reviews->links() }}
            </div>
        </div>

    </div>
</x-app-layout>