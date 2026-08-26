@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto py-8 px-4">

    <div class="mb-6">
        <a
            href="{{ route('admin.survey-templates.index') }}"
            class="text-sm font-bold text-gray-500 hover:text-green-600"
        >
            ← 業種・テンプレート一覧へ戻る
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h1 class="text-xl font-bold text-gray-900">
                業種を編集
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                業種名を変更できます。
            </p>
        </div>

        <div class="p-6">

            <form
                method="POST"
                action="{{ route('admin.survey-templates.industries.update', $industry) }}"
            >
                @csrf
                @method('PUT')

                <div class="mb-6">

                    <label
                        for="name"
                        class="block text-sm font-bold text-gray-700 mb-2"
                    >
                        業種名
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $industry->name) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-green-500 focus:border-green-500"
                        required
                        maxlength="255"
                    >

                    @error('name')
                        <p class="text-sm text-red-500 mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div class="flex justify-end gap-3">

                    <a
                        href="{{ route('admin.survey-templates.index') }}"
                        class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50"
                    >
                        キャンセル
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold"
                    >
                        保存する
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection