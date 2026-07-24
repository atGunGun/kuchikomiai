<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coel(コエル)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased text-gray-800">
    
    @auth
    <nav class="bg-white border-b border-gray-200 fixed z-30 w-full shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[4rem] py-2">
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-xl font-extrabold text-green-600 tracking-tight flex items-center gap-2">
                    <img src="/img/logo.svg" alt="Coel" class="h-10" style="width: 80px;">
                    </a>
                </div>
                
                <div class="flex flex-wrap justify-end items-center gap-3 sm:gap-6 ml-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600 font-bold text-xs sm:text-sm transition-colors whitespace-nowrap">ダッシュボード</a>
                    <a href="{{ route('surveys.index') }}" class="text-gray-600 hover:text-green-600 font-bold text-xs sm:text-sm transition-colors whitespace-nowrap">アンケート管理</a>
                    <a href="{{ route('settings.edit') }}" class="text-gray-600 hover:text-green-600 font-bold text-xs sm:text-sm transition-colors whitespace-nowrap">店舗設定</a>
                    
                    <form method="POST" action="/logout" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-red-500 border border-red-200 hover:bg-red-50 font-bold text-xs sm:text-sm px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <div class="{{ auth()->check() ? 'pt-20' : 'pt-10' }} pb-10">
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
    
</body>
</html>