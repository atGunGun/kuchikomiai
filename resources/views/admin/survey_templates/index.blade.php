@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto py-8 px-4">

    <h1 class="text-2xl font-bold mb-6">
        業種別アンケートテンプレート管理
    </h1>

    {{-- 業種追加 --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">

        <h2 class="text-lg font-bold mb-4">
            業種を追加
        </h2>

        <form method="POST" action="{{ route('admin.survey-templates.industries.store') }}">
            @csrf

            <div class="flex gap-3">

                <input
                    type="text"
                    name="name"
                    placeholder="例：飲食店"
                    class="border rounded px-3 py-2 flex-1"
                    required
                >

                <button
                    type="submit"
                    class="bg-green-600 text-white px-5 py-2 rounded font-bold"
                >
                    業種を追加
                </button>

            </div>
        </form>

    </div>


    {{-- 業種一覧 --}}
    <div class="space-y-6">

        @forelse($industries as $industry)

            <div class="bg-white rounded-lg shadow p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">

                    <h2 class="text-xl font-bold">
                        {{ $industry->name }}
                    </h2>

                    <div class="flex items-center gap-2">

                        {{-- 業種編集 --}}
                        <a
                            href="{{ route('admin.survey-templates.industries.edit', $industry) }}"
                            class="border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-bold"
                        >
                            編集
                        </a>

                        {{-- 業種削除 --}}
                        <form
                            method="POST"
                            action="{{ route('admin.survey-templates.industries.destroy', $industry) }}"
                            onsubmit="return confirm('「{{ $industry->name }}」を削除しますか？');"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold"
                            >
                                削除
                            </button>

                        </form>

                        {{-- テンプレート作成 --}}
                        <a
                            href="{{ route('admin.survey-templates.create', $industry) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold"
                        >
                            テンプレートを作成
                        </a>

                    </div>

                </div>


                {{-- テンプレート一覧 --}}
                @if($industry->surveyTemplates->count())

                    <div class="space-y-3">

                        @foreach($industry->surveyTemplates as $template)

                            <div class="border rounded-lg p-4">

                                <div class="flex items-center justify-between">

                                    <div>

                                        <h3 class="font-bold">
                                            {{ $template->title }}
                                        </h3>

                                        @if($template->description)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $template->description }}
                                            </p>
                                        @endif

                                        <p class="text-sm text-gray-500 mt-2">
                                            設問数：
                                            {{ $template->questions->count() }}
                                        </p>

                                    </div>

                                    <div class="flex gap-2">

                                        <a
                                            href="{{ route('admin.survey-templates.edit', $template) }}"
                                            class="border px-4 py-2 rounded"
                                        >
                                            編集
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.survey-templates.destroy', $template) }}"
                                            onsubmit="return confirm('このテンプレートを削除しますか？');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-600 text-white px-4 py-2 rounded"
                                            >
                                                削除
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="text-gray-500">
                        まだテンプレートがありません。
                    </p>

                @endif

            </div>

        @empty

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-500">
                    業種がまだ登録されていません。
                </p>
            </div>

        @endforelse

    </div>

</div>

@endsection