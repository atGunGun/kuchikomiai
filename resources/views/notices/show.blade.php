<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10">
                <div class="mb-6">
                    <span class="text-blue-600 font-bold">[{{ $notice->category->name ?? 'お知らせ' }}]</span>
                    <span class="text-gray-400 ml-2">{{ $notice->created_at->format('Y/m/d') }}</span>
                </div>
                <h1 class="text-3xl font-extrabold mb-8">{{ $notice->title }}</h1>
                <hr class="mb-8">
                <div class="prose max-w-none">
                    {!! $notice->content !!}
                </div>
                <div class="mt-12">
                    <a href="{{ route('notices.index') }}" class="text-gray-500 hover:underline">← 一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>