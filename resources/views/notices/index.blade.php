<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">お知らせ一覧</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-600">全 {{ $notices->total() }} 件</p>
                    
                    <form action="{{ route('notices.index') }}" method="GET" id="perPageForm" class="flex items-center text-sm">
                        <label for="per_page" class="mr-2">表示件数:</label>
                        <select name="per_page" onchange="document.getElementById('perPageForm').submit();" class="border-gray-300 rounded shadow-sm focus:ring-blue-500">
                            @foreach([5, 10, 20, 50, 100] as $num)
                                <option value="{{ $num }}" {{ $perPage == $num ? 'selected' : '' }}>{{ $num }}件</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">日付</th>
                            <th class="text-left py-2">カテゴリ</th>
                            <th class="text-left py-2">タイトル</th>
                            <th class="text-right py-2">詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notices as $notice)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-4 text-sm text-gray-500">{{ $notice->created_at->format('Y/m/d') }}</td>
                            <td class="py-4">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">{{ $notice->category->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 font-bold">{{ $notice->title }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('notices.show', $notice->id) }}" class="text-blue-600 hover:underline">詳細を見る →</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $notices->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>