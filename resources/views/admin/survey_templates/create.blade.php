@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- 戻る --}}
    <div class="mb-6">
        <a
            href="{{ route('admin.survey-templates.index') }}"
            class="text-gray-600 hover:text-green-600"
        >
            ← 業種・テンプレート一覧に戻る
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">

        <h1 class="text-2xl font-bold mb-2">
            アンケートテンプレートを作成
        </h1>

        <p class="text-gray-600 mb-8">
            業種：
            <span class="font-bold text-gray-900">
                {{ $industry->name }}
            </span>
        </p>


        {{-- エラーメッセージ --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form
            method="POST"
            action="{{ route('admin.survey-templates.store', $industry) }}"
        >
            @csrf


            {{-- テンプレート基本情報 --}}
            <div class="mb-8">

                <h2 class="text-lg font-bold border-b pb-2 mb-4">
                    テンプレート情報
                </h2>

                <div class="mb-4">

                    <label class="block font-bold mb-2">
                        テンプレート名
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="例：飲食店向け口コミアンケート"
                        class="w-full border rounded-lg px-4 py-3"
                        required
                    >

                </div>


                <div>

                    <label class="block font-bold mb-2">
                        説明
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="このテンプレートについての説明"
                        class="w-full border rounded-lg px-4 py-3"
                    >{{ old('description') }}</textarea>

                </div>

            </div>


            {{-- 設問 --}}
            <div>

                <div class="flex items-center justify-between mb-4">

                    <h2 class="text-lg font-bold border-b pb-2 flex-1">
                        設問
                    </h2>

                    <button
                        type="button"
                        id="add-question"
                        class="ml-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold"
                    >
                        ＋ 設問を追加
                    </button>

                </div>


                <div id="questions-container" class="space-y-6">

                    {{-- 最初の設問 --}}
                    <div class="question-item border rounded-lg p-5">

                        <div class="flex items-center justify-between mb-4">

                            <h3 class="font-bold">
                                設問 <span class="question-number">1</span>
                            </h3>

                            <button
                                type="button"
                                class="remove-question text-red-600 hover:text-red-800 font-bold"
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
                                name="questions[0][text]"
                                placeholder="例：料理の味はいかがでしたか？"
                                class="w-full border rounded-lg px-4 py-3"
                                required
                            >

                        </div>


                        {{-- タイプ --}}
                        <div class="mb-4">

                            <label class="block font-bold mb-2">
                                回答タイプ
                            </label>

                            <select
                                name="questions[0][type]"
                                class="question-type w-full border rounded-lg px-4 py-3"
                            >
                                <option value="text">
                                    一行テキスト
                                </option>

                                <option value="textarea">
                                    長文テキスト
                                </option>

                                <option value="radio">
                                    ラジオボタン（1つ選択）
                                </option>

                                <option value="checkbox">
                                    チェックボックス（複数選択）
                                </option>
                            </select>

                        </div>


                        {{-- 必須 --}}
                        <div class="mb-4">

                            <label class="inline-flex items-center">

                                <input
                                    type="checkbox"
                                    name="questions[0][is_required]"
                                    value="1"
                                    class="mr-2"
                                >

                                <span>
                                    必須回答
                                </span>

                            </label>

                        </div>


                        {{-- 選択肢 --}}
                        <div class="options-area hidden">

                            <label class="block font-bold mb-2">
                                選択肢
                            </label>

                            <div class="options-container space-y-2">

                                <div class="flex gap-2 option-item">

                                    <input
                                        type="text"
                                        name="questions[0][options][]"
                                        placeholder="例：とても良い"
                                        class="flex-1 border rounded-lg px-4 py-2"
                                    >

                                    <button
                                        type="button"
                                        class="remove-option border border-red-300 text-red-600 px-3 rounded"
                                    >
                                        削除
                                    </button>

                                </div>

                                <div class="flex gap-2 option-item">

                                    <input
                                        type="text"
                                        name="questions[0][options][]"
                                        placeholder="例：良い"
                                        class="flex-1 border rounded-lg px-4 py-2"
                                    >

                                    <button
                                        type="button"
                                        class="remove-option border border-red-300 text-red-600 px-3 rounded"
                                    >
                                        削除
                                    </button>

                                </div>

                            </div>

                            <button
                                type="button"
                                class="add-option mt-3 border border-blue-500 text-blue-600 px-4 py-2 rounded-lg"
                            >
                                ＋ 選択肢を追加
                            </button>

                        </div>

                    </div>

                </div>

            </div>


            {{-- 保存 --}}
            <div class="mt-8 pt-6 border-t flex justify-end gap-3">

                <a
                    href="{{ route('admin.survey-templates.index') }}"
                    class="border px-5 py-3 rounded-lg"
                >
                    キャンセル
                </a>

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold"
                >
                    テンプレートを保存
                </button>

            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('questions-container');
    const addQuestionButton = document.getElementById('add-question');


    /*
    |--------------------------------------------------------------------------
    | 設問番号を更新
    |--------------------------------------------------------------------------
    */

    function updateQuestionNumbers() {

        const questions = container.querySelectorAll('.question-item');

        questions.forEach((question, index) => {

            question.querySelector('.question-number').textContent = index + 1;

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 選択肢エリアの表示・非表示
    |--------------------------------------------------------------------------
    */

    function updateOptionsArea(question) {

        const type = question.querySelector('.question-type').value;
        const optionsArea = question.querySelector('.options-area');

        if (type === 'radio' || type === 'checkbox') {

            optionsArea.classList.remove('hidden');

        } else {

            optionsArea.classList.add('hidden');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 設問イベント
    |--------------------------------------------------------------------------
    */

    function setupQuestion(question) {

        const typeSelect = question.querySelector('.question-type');

        typeSelect.addEventListener('change', function () {

            updateOptionsArea(question);

        });


        const removeButton = question.querySelector('.remove-question');

        removeButton.addEventListener('click', function () {

            const questions = container.querySelectorAll('.question-item');

            if (questions.length <= 1) {

                alert('設問は最低1つ必要です。');

                return;

            }

            question.remove();

            reindexQuestions();

        });


        /*
        |--------------------------------------------------------------------------
        | 選択肢追加
        |--------------------------------------------------------------------------
        */

        const addOptionButton = question.querySelector('.add-option');

        if (addOptionButton) {

            addOptionButton.addEventListener('click', function () {

                const optionsContainer =
                    question.querySelector('.options-container');

                const questionIndex =
                    Array.from(
                        container.querySelectorAll('.question-item')
                    ).indexOf(question);

                const option = document.createElement('div');

                option.className = 'flex gap-2 option-item';

                option.innerHTML = `
                    <input
                        type="text"
                        name="questions[${questionIndex}][options][]"
                        placeholder="選択肢"
                        class="flex-1 border rounded-lg px-4 py-2"
                    >

                    <button
                        type="button"
                        class="remove-option border border-red-300 text-red-600 px-3 rounded"
                    >
                        削除
                    </button>
                `;

                optionsContainer.appendChild(option);

                setupOptionRemove(option);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | 選択肢削除
        |--------------------------------------------------------------------------
        */

        question
            .querySelectorAll('.remove-option')
            .forEach(function (button) {

                setupOptionRemove(button.closest('.option-item'));

            });


        updateOptionsArea(question);

    }


    /*
    |--------------------------------------------------------------------------
    | 選択肢削除イベント
    |--------------------------------------------------------------------------
    */

    function setupOptionRemove(option) {

        const button = option.querySelector('.remove-option');

        button.addEventListener('click', function () {

            const options =
                option.parentElement.querySelectorAll('.option-item');

            if (options.length <= 1) {

                alert('選択肢は最低1つ必要です。');

                return;

            }

            option.remove();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 設問追加
    |--------------------------------------------------------------------------
    */

    addQuestionButton.addEventListener('click', function () {

        const index =
            container.querySelectorAll('.question-item').length;

        const question = document.createElement('div');

        question.className = 'question-item border rounded-lg p-5';

        question.innerHTML = `
            <div class="flex items-center justify-between mb-4">

                <h3 class="font-bold">
                    設問 <span class="question-number"></span>
                </h3>

                <button
                    type="button"
                    class="remove-question text-red-600 hover:text-red-800 font-bold"
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
                    placeholder="質問文を入力してください"
                    class="w-full border rounded-lg px-4 py-3"
                    required
                >

            </div>


            <div class="mb-4">

                <label class="block font-bold mb-2">
                    回答タイプ
                </label>

                <select
                    name="questions[${index}][type]"
                    class="question-type w-full border rounded-lg px-4 py-3"
                >

                    <option value="text">
                        一行テキスト
                    </option>

                    <option value="textarea">
                        長文テキスト
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

                <label class="inline-flex items-center">

                    <input
                        type="checkbox"
                        name="questions[${index}][is_required]"
                        value="1"
                        class="mr-2"
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

                    <div class="flex gap-2 option-item">

                        <input
                            type="text"
                            name="questions[${index}][options][]"
                            placeholder="選択肢"
                            class="flex-1 border rounded-lg px-4 py-2"
                        >

                        <button
                            type="button"
                            class="remove-option border border-red-300 text-red-600 px-3 rounded"
                        >
                            削除
                        </button>

                    </div>

                </div>

                <button
                    type="button"
                    class="add-option mt-3 border border-blue-500 text-blue-600 px-4 py-2 rounded-lg"
                >
                    ＋ 選択肢を追加
                </button>

            </div>
        `;

        container.appendChild(question);

        setupQuestion(question);

        updateQuestionNumbers();

    });


    /*
    |--------------------------------------------------------------------------
    | 設問のname属性を振り直す
    |--------------------------------------------------------------------------
    */

    function reindexQuestions() {

        const questions =
            container.querySelectorAll('.question-item');

        questions.forEach(function (question, index) {

            question
                .querySelector('input[name$="[text]"]')
                .name = `questions[${index}][text]`;


            question
                .querySelector('.question-type')
                .name = `questions[${index}][type]`;


            const required =
                question.querySelector('input[type="checkbox"]');

            required.name =
                `questions[${index}][is_required]`;


            question
                .querySelectorAll('.options-container input')
                .forEach(function (input) {

                    input.name =
                        `questions[${index}][options][]`;

                });

        });

        updateQuestionNumbers();

    }


    /*
    |--------------------------------------------------------------------------
    | 初期設定
    |--------------------------------------------------------------------------
    */

    container
        .querySelectorAll('.question-item')
        .forEach(function (question) {

            setupQuestion(question);

        });

});

</script>

@endsection