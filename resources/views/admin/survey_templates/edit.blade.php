@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto py-8 px-4">

    <div class="mb-6">
        <a
            href="{{ route('admin.survey-templates.index') }}"
            class="text-sm text-gray-600 hover:text-blue-600"
        >
            ← テンプレート一覧に戻る
        </a>
    </div>

    <h1 class="text-2xl font-bold mb-6">
        アンケートテンプレート編集
    </h1>

    {{-- 業種 --}}
    <div class="bg-gray-100 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-500">
            業種
        </p>

        <p class="font-bold text-lg">
            {{ $template->industry->name }}
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('admin.survey-templates.update', $template) }}"
    >
        @csrf
        @method('PUT')

        {{-- テンプレート情報 --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">

            <h2 class="text-lg font-bold mb-4">
                テンプレート情報
            </h2>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    テンプレート名
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $template->title) }}"
                    class="border rounded px-3 py-2 w-full"
                    required
                >

                @error('title')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div>

                <label class="block font-bold mb-2">
                    説明
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="border rounded px-3 py-2 w-full"
                >{{ old('description', $template->description) }}</textarea>

                @error('description')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        {{-- 設問 --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">

            <div class="flex items-center justify-between mb-4">

                <h2 class="text-lg font-bold">
                    設問
                </h2>

                <button
                    type="button"
                    id="add-question"
                    class="bg-blue-600 text-white px-4 py-2 rounded font-bold"
                >
                    ＋ 設問を追加
                </button>

            </div>

            <div
                id="questions-container"
                class="space-y-6"
            >

                @foreach($template->questions as $index => $question)

                    <div
                        class="question-item border rounded-lg p-5"
                    >

                        <div class="flex justify-between items-center mb-4">

                            <h3 class="font-bold">
                                設問 <span class="question-number">{{ $index + 1 }}</span>
                            </h3>

                            <button
                                type="button"
                                class="remove-question text-red-600 font-bold"
                            >
                                削除
                            </button>

                        </div>


                        {{-- 質問文 --}}
                        <div class="mb-4">

                            <label class="block font-bold mb-2">
                                質問文
                            </label>

                            <input
                                type="text"
                                name="questions[{{ $index }}][text]"
                                value="{{ old("questions.$index.text", $question->question_text) }}"
                                class="border rounded px-3 py-2 w-full question-text"
                                required
                            >

                        </div>


                        {{-- 回答タイプ --}}
                        <div class="mb-4">

                            <label class="block font-bold mb-2">
                                回答タイプ
                            </label>

                            <select
                                name="questions[{{ $index }}][type]"
                                class="border rounded px-3 py-2 w-full question-type"
                            >

                                <option
                                    value="text"
                                    {{ $question->type === 'text' ? 'selected' : '' }}
                                >
                                    テキスト
                                </option>

                                <option
                                    value="textarea"
                                    {{ $question->type === 'textarea' ? 'selected' : '' }}
                                >
                                    長文
                                </option>

                                <option
                                    value="radio"
                                    {{ $question->type === 'radio' ? 'selected' : '' }}
                                >
                                    ラジオボタン（1つ選択）
                                </option>

                                <option
                                    value="checkbox"
                                    {{ $question->type === 'checkbox' ? 'selected' : '' }}
                                >
                                    チェックボックス（複数選択）
                                </option>

                            </select>

                        </div>


                        {{-- 必須 --}}
                        <div class="mb-4">

                            <label class="inline-flex items-center gap-2">

                                <input
                                    type="checkbox"
                                    name="questions[{{ $index }}][is_required]"
                                    value="1"
                                    {{ $question->is_required ? 'checked' : '' }}
                                >

                                <span>
                                    必須回答
                                </span>

                            </label>

                        </div>


                        {{-- 選択肢 --}}
                        <div
                            class="options-area {{ in_array($question->type, ['radio', 'checkbox']) ? '' : 'hidden' }}"
                        >

                            <label class="block font-bold mb-2">
                                選択肢
                            </label>

                            <div class="options-container space-y-2">

                                @foreach(($question->options ?? []) as $optionIndex => $option)

                                    <div class="flex gap-2 option-row">

                                        <input
                                            type="text"
                                            name="questions[{{ $index }}][options][]"
                                            value="{{ $option }}"
                                            class="border rounded px-3 py-2 flex-1"
                                        >

                                        <button
                                            type="button"
                                            class="remove-option text-red-600 font-bold px-2"
                                        >
                                            削除
                                        </button>

                                    </div>

                                @endforeach

                            </div>

                            <button
                                type="button"
                                class="add-option mt-3 border px-3 py-2 rounded"
                            >
                                ＋ 選択肢を追加
                            </button>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- 保存 --}}
        <div class="flex justify-end gap-3">

            <a
                href="{{ route('admin.survey-templates.index') }}"
                class="border px-5 py-3 rounded font-bold"
            >
                キャンセル
            </a>

            <button
                type="submit"
                class="bg-green-600 text-white px-6 py-3 rounded font-bold"
            >
                テンプレートを更新
            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('questions-container');
    const addQuestionButton = document.getElementById('add-question');

    let questionIndex = {{ $template->questions->count() }};


    // -----------------------------
    // 設問追加
    // -----------------------------

    addQuestionButton.addEventListener('click', function () {

        const index = questionIndex++;

        const html = `
            <div class="question-item border rounded-lg p-5">

                <div class="flex justify-between items-center mb-4">

                    <h3 class="font-bold">
                        設問 <span class="question-number"></span>
                    </h3>

                    <button
                        type="button"
                        class="remove-question text-red-600 font-bold"
                    >
                        削除
                    </button>

                </div>

                <div class="mb-4">

                    <label class="block font-bold mb-2">
                        質問文
                    </label>

                    <input
                        type="text"
                        name="questions[${index}][text]"
                        class="border rounded px-3 py-2 w-full"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="block font-bold mb-2">
                        回答タイプ
                    </label>

                    <select
                        name="questions[${index}][type]"
                        class="border rounded px-3 py-2 w-full question-type"
                    >
                        <option value="text">
                            テキスト
                        </option>

                        <option value="textarea">
                            長文
                        </option>

                        <option value="radio">
                            ラジオボタン（1つ選択）
                        </option>

                        <option value="checkbox">
                            チェックボックス（複数選択）
                        </option>
                    </select>

                </div>

                <div class="mb-4">

                    <label class="inline-flex items-center gap-2">

                        <input
                            type="checkbox"
                            name="questions[${index}][is_required]"
                            value="1"
                        >

                        <span>
                            必須回答
                        </span>

                    </label>

                </div>

                <div class="options-area hidden">

                    <label class="block font-bold mb-2">
                        選択肢
                    </label>

                    <div class="options-container space-y-2">

                    </div>

                    <button
                        type="button"
                        class="add-option mt-3 border px-3 py-2 rounded"
                    >
                        ＋ 選択肢を追加
                    </button>

                </div>

            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        updateQuestionNumbers();

    });


    // -----------------------------
    // 設問削除
    // -----------------------------

    document.addEventListener('click', function (event) {

        if (event.target.classList.contains('remove-question')) {

            const item = event.target.closest('.question-item');

            if (document.querySelectorAll('.question-item').length <= 1) {

                alert('設問は最低1つ必要です。');

                return;
            }

            item.remove();

            updateQuestionNumbers();
        }

    });


    // -----------------------------
    // 回答タイプ変更
    // -----------------------------

    document.addEventListener('change', function (event) {

        if (event.target.classList.contains('question-type')) {

            const question = event.target.closest('.question-item');
            const optionsArea = question.querySelector('.options-area');

            if (
                event.target.value === 'radio' ||
                event.target.value === 'checkbox'
            ) {

                optionsArea.classList.remove('hidden');

            } else {

                optionsArea.classList.add('hidden');

            }

        }

    });


    // -----------------------------
    // 選択肢追加
    // -----------------------------

    document.addEventListener('click', function (event) {

        if (event.target.classList.contains('add-option')) {

            const question = event.target.closest('.question-item');

            const select = question.querySelector('.question-type');

            const index = select.name.match(/questions\[(\d+)\]/)[1];

            const optionsContainer =
                question.querySelector('.options-container');

            const html = `
                <div class="flex gap-2 option-row">

                    <input
                        type="text"
                        name="questions[${index}][options][]"
                        class="border rounded px-3 py-2 flex-1"
                    >

                    <button
                        type="button"
                        class="remove-option text-red-600 font-bold px-2"
                    >
                        削除
                    </button>

                </div>
            `;

            optionsContainer.insertAdjacentHTML('beforeend', html);

        }

    });


    // -----------------------------
    // 選択肢削除
    // -----------------------------

    document.addEventListener('click', function (event) {

        if (event.target.classList.contains('remove-option')) {

            event.target.closest('.option-row').remove();

        }

    });


    // -----------------------------
    // 設問番号更新
    // -----------------------------

    function updateQuestionNumbers() {

        document
            .querySelectorAll('.question-item')
            .forEach(function (item, index) {

                const number =
                    item.querySelector('.question-number');

                if (number) {
                    number.textContent = index + 1;
                }

            });

    }


    updateQuestionNumbers();

});

</script>

@endsection