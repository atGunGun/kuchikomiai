<!DOCTYPE html>
<html lang="ja">
<x-app-layout>
    <div class="py-12">
        <div class="plan_inner">
            <body>
                <h1 class="art_tit"><span><img src="/img/plan.svg" alt="" class="h-10"></span>お知らせ管理</h1>
                <a href="{{ route('dashboard') }}" class="re_btn">← ダッシュボードへ戻る</a>

                @if (session('success'))
                    <p style="color: green;" class="arat mb-4"><b>{{ session('success') }}</b></p>
                @endif
                @if (session('error'))
                    <p style="color: red;" class="arat mb-4"><b>{{ session('error') }}</b></p>
                @endif

                <div class="whi_box news_cate shadow-sm">
                    <h3>1. カテゴリの追加</h3>
                    <form action="{{ route('admin.notices.category.store') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="text" name="name" placeholder="新カテゴリ名" required class="border-gray-300 rounded-lg">
                        <button type="submit" class="sub_btn">追加</button>
                    </form>
                    
                    <div class="list_box">
                        <h4>カテゴリ一覧</h4>
                        <ul class="mt-2 space-y-2">
                            @foreach($categories as $category) 
                                <li class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-100">
                                    <span class="text-sm font-bold text-gray-700">{{ $category->name }}</span>
                                    
                                    {{-- カテゴリの削除ボタン --}}
                                    <form action="{{ route('admin.notices.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('本当にこのカテゴリを削除しますか？\n※お知らせが紐づいている場合は削除できません。');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-white border border-red-200 px-2 py-1 rounded text-xs font-bold transition">削除</button>
                                    </form>
                                </li> 
                            @endforeach
                        </ul>
                    </div>

                    <h3 class="mt-8">2. お知らせの投稿</h3>
                    <a href="{{ route('admin.notices.create') }}" class="sub_btn type02 block text-center"><button>＋ 新規お知らせを作成</button></a>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-500 bg-white border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 font-bold">日付・カテゴリ</th>
                                    <th class="px-6 py-3 font-bold">タイトル・対象</th>
                                    <th class="px-6 py-3 font-bold text-right">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    // ページネーション（links）を使えるように get() から paginate() に変更するとより実用的です
                                    $recentNotices = \App\Models\Notice::with('category')->latest()->take(20)->get();
                                @endphp
                                @forelse($recentNotices as $notice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <p class="text-xs text-gray-400 font-medium mb-1">{{ $notice->created_at->format('Y/m/d') }}</p>
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded font-medium">{{ $notice->category->name ?? '未分類' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-800">{{ $notice->title }}</p>
                                        <p class="text-xs text-blue-500 mt-1">{{ $notice->target_role === 'all' ? '全員対象' : $notice->target_role }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- お知らせの編集ボタン --}}
                                            <a href="{{ route('admin.notices.edit', $notice->id) }}" class="text-blue-600 hover:text-blue-800 font-bold px-3 py-1.5 bg-blue-50 rounded-lg text-xs transition">編集</a>
                                            
                                            {{-- お知らせの削除ボタン --}}
                                            <form action="{{ route('admin.notices.destroy', $notice->id) }}" method="POST" onsubmit="return confirm('本当にこの記事を削除しますか？');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold px-3 py-1.5 bg-red-50 rounded-lg text-xs transition">削除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">まだ投稿されたお知らせはありません。</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </body>
        </div>
    </div>
</x-app-layout>
</html>