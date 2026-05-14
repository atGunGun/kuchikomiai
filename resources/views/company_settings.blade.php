<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="text-2xl">⚙️</span> 店舗設定
            </h1>
            <p class="text-gray-500 mt-1">店舗の基本情報を変更できます。</p>
            <p class="text-sm text-green-600 font-medium mt-2 bg-green-50 inline-block px-3 py-1 rounded-lg border border-green-100">
                💡 アンケートの設問設定は「アンケート管理」メニューから行えるようになりました！
            </p>
        </div>

        @if(session('success'))
            <div class="p-4 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2 animate-fade-in" role="alert">
                <span class="text-lg font-bold">✓</span> 
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ url('/settings') }}" method="POST">
            @csrf
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-4">基本情報</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">店舗名 <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $company->name }}" required 
                               class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-3 outline-none transition-all"
                               placeholder="例：口コミカフェ 渋谷店">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">住所</label>
                        <input type="text" name="address" value="{{ $company->address ?? '' }}" 
                               class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-3 outline-none transition-all"
                               placeholder="例：東京都渋谷区〇〇 1-2-3">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Google Map URL</label>
                        <input type="url" name="google_map_url" value="{{ $company->google_map_url ?? '' }}" 
                               class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-green-500 focus:border-green-500 block w-full p-3 outline-none transition-all"
                               placeholder="例：https://maps.app.goo.gl/...">
                        <p class="mt-2 text-xs text-gray-500 font-medium">※お客様に案内するGoogle Mapの共有リンク（URL）を入力してください。</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-extrabold rounded-2xl text-base px-10 py-3 shadow-md shadow-green-200 transition-all transform hover:-translate-y-0.5">
                    設定を保存する
                </button>
            </div>
        </form>
    </div>
</x-app-layout>