<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h2 class="text-lg font-bold mb-4">システム設定</h2>
                <form action="{{ route('admin.settings.global.update') }}" method="POST">
                    @csrf
                    <div>
                        <label>お知らせの1ページ表示件数:</label>
                        <input type="number" name="notice_per_page" value="{{ $noticePerPage }}" class="border rounded px-2 py-1">
                    </div>
                    <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">保存する</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>