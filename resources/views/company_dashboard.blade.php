<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <h1 class="text-xl font-bold text-gray-800">📊 分析ダッシュボード</h1>
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="filter" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block p-2.5">
                    <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>今月</option>
                    <option value="last_month" {{ $filter == 'last_month' ? 'selected' : '' }}>先月</option>
                    <option value="last_3_months" {{ $filter == 'last_3_months' ? 'selected' : '' }}>直近3ヶ月</option>
                    <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>期間指定</option>
                </select>
                @if($filter == 'custom')
                    <input type="date" name="start_date" class="text-sm rounded-xl border-gray-200" value="{{ request('start_date') }}">
                    <input type="date" name="end_date" class="text-sm rounded-xl border-gray-200" value="{{ request('end_date') }}">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm">適用</button>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">回答数</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ $stats['total_count'] }}<span class="text-sm ml-1">件</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">コピー率</p>
                <p class="text-3xl font-extrabold text-blue-600">{{ $stats['copy_rate'] }}<span class="text-sm ml-1">%</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">Google遷移率</p>
                <p class="text-3xl font-extrabold text-orange-500">{{ $stats['redirect_rate'] }}<span class="text-sm ml-1">%</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">平均所要時間</p>
                <p class="text-3xl font-extrabold text-green-600">{{ $stats['avg_duration'] }}<span class="text-sm ml-1">分</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">✨ 最新の口コミ</h3>
                        <a href="{{ route('reviews.index') }}" class="text-sm text-green-600 font-bold hover:underline">すべて見る →</a>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($latestReviews as $review)
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs text-gray-400 mb-1">{{ $review->created_at->diffForHumans() }}</p>
                                <p class="text-sm text-gray-700 line-clamp-2 italic">"{{ $review->generated_text }}"</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-6">📊 項目別回答集計</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($surveyStats as $questionText => $counts)
                        <div>
                            <p class="text-sm font-bold text-gray-600 mb-3 border-l-4 border-green-500 pl-2">{{ $questionText }}</p>
                            <div class="space-y-2">
                                @foreach($counts as $label => $count)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">{{ $label }}</span>
                                    <div class="flex items-center gap-2 flex-1 mx-4">
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $stats['total_count'] > 0 ? ($count / $stats['total_count']) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-700 w-8 text-right">{{ $count }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <h3 class="font-bold text-gray-800 mb-4">📱 店頭掲示用QR</h3>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 inline-block mb-4">
                        {!! QrCode::size(150)->generate($reviewUrl) !!}
                    </div>
                    <input type="text" readonly value="{{ $reviewUrl }}" class="w-full text-xs p-2 bg-gray-50 border-gray-200 rounded-lg text-center">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>