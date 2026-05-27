<x-app-layout>
    <div class="space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-red-100 border-t-4 border-t-red-600">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-3xl text-red-600">👑</span> 運営マスター管理
                </h1>
                <p class="text-gray-500 mt-1">システム全体の設定や企業データの管理を行います。</p>
            </div>
            
            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                <a href="{{ route('admin.plans.index') }}" class="text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 px-5 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <span>📋</span> プラン管理
                </a>
                <a href="{{ route('admin.settings.global') }}" class="text-sm font-bold text-white bg-gray-700 hover:bg-gray-800 px-5 py-2.5 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <span>⚙️</span> システム設定
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="p-4 bg-red-50 text-red-600 rounded-xl text-3xl">🏢</div>
                <div>
                    <p class="text-sm text-gray-500 font-bold tracking-wider">登録企業総数</p>
                    <p class="text-3xl font-extrabold text-gray-900">{{ $companyCount }} <span class="text-base text-gray-500 font-medium">件</span></p>
                </div>
            </div>
            </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span>📢</span> お知らせ管理
                    </h2>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.notices.index') }}" class="text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition-colors">一覧・カテゴリ</a>
                        <a href="{{ route('admin.notices.create') }}" class="text-xs font-bold text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded-lg transition-colors shadow-sm">＋ 新規投稿</a>
                    </div>
                </div>
                
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
                                $recentNotices = \App\Models\Notice::with('category')->latest()->take(5)->get();
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
                                    <a href="{{ route('admin.notices.edit', $notice->id) }}" class="text-blue-600 hover:text-blue-800 font-bold px-3 py-1.5 bg-blue-50 rounded-lg text-xs">編集</a>
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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span>🏢</span> 企業一覧
                    </h2>
                </div>
                
                <div class="overflow-y-auto max-h-[400px]">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 bg-white border-b border-gray-100 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 font-bold">企業名</th>
                                <th class="px-6 py-3 font-bold">担当代理店</th>
                                <th class="px-6 py-3 font-bold text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($companies as $company)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ $company->name }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($company->agency)
                                        <span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-md text-xs font-bold">{{ $company->agency->name }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs font-medium border border-gray-200 px-2.5 py-1 rounded-md">直販</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="text-blue-600 hover:text-blue-800 font-bold px-3 py-1.5 bg-blue-50 rounded-lg text-xs">編集</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>