<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'agency')
    <div style="background: #eef; padding: 15px; border-radius: 5px; margin-top: 20px;">
        <h3>あなたの代理店紹介用URL</h3>
        <p>このURLから登録してもらうと、自動的にあなたの案件として紐付けられます。</p>
        <code>{{ url('/register') }}?aid={{ auth()->id() }}</code>
    </div>
    @endif

</x-app-layout>
