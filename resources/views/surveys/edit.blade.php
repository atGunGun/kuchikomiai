<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="text-2xl">✏️</span> アンケートを編集
                </h1>
                <p class="text-gray-500 mt-1">{{ $survey->title }} の内容を変更します。</p>
            </div>
            <a href="{{ route('surveys.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700">キャンセル</a>
        </div>

        <form action="{{ route('surveys.update', $survey->id) }}" method="POST" id="survey-form">
            @csrf
            @method('PUT')
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-4">基本情報</h2>
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">タイトル</label>
                        <input type="text" name="title" value="{{ $survey->title }}" required class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-3">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">説明文</label>
                        <textarea name="description" rows="2" class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-3">{{ $survey->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">設問の設定</h2>
                    <span class="text-xs text-gray-500 font-medium">左端の「≡」をドラッグして並び替え</span>
                </div>

                <div id="questions-container" class="space-y-4">
                    @foreach($survey->questions as $index => $question)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative group flex gap-4">
                            <div class="sort-handle flex items-center justify-center cursor-move text-gray-300 hover:text-green-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <label class="block mb-2 text-xs font-bold text-gray-400 uppercase">設問文</label>
                                        <input type="text" name="questions[{{ $index }}][text]" value="{{ $question->question_text }}" required class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-2.5">
                                    </div>
                                    <div class="w-full md:w-48">
                                        <label class="block mb-2 text-xs font-bold text-gray-400 uppercase">回答形式</label>
                                        <select name="questions[{{ $index }}][type]" onchange="toggleOptions(this, {{ $index }})" class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-2.5">
                                            <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>短いテキスト</option>
                                            <option value="textarea" {{ $question->type == 'textarea' ? 'selected' : '' }}>長いテキスト</option>
                                            <option value="checkbox" {{ $question->type == 'checkbox' ? 'selected' : '' }}>複数選択（チェックボックス）</option>
                                            <option value="radio" {{ $question->type == 'radio' ? 'selected' : '' }}>1つ選択（ラジオボタン）</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end pb-1">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="questions[{{ $index }}][is_required]" value="1" class="sr-only peer" {{ $question->is_required ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                            <span class="ms-2 text-xs font-bold text-gray-500">必須</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="options-container-{{ $index }}" class="mt-4 p-4 bg-green-50 rounded-xl {{ in_array($question->type, ['checkbox', 'radio']) ? '' : 'hidden' }}">
                                    <label class="block mb-2 text-xs font-bold text-brand uppercase">選択肢</label>
                                    <div id="options-list-{{ $index }}" class="space-y-2 mb-3">
                                        @if(is_array($question->options))
                                            @foreach($question->options as $option)
                                                <div class="flex items-center gap-2">
                                                    <input type="text" name="questions[{{ $index }}][options][]" value="{{ $option }}" class="bg-white border border-green-200 text-sm rounded-lg focus:ring-green-500 block w-full p-2">
                                                    <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-red-500 px-2 font-bold">✕</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addOption({{ $index }})" class="text-xs font-bold text-green-600 bg-white border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                                        ＋ 選択肢を追加
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="this.parentElement.remove();" class="absolute -top-3 -right-3 bg-white border border-gray-200 text-gray-400 hover:text-white hover:bg-red-500 w-8 h-8 rounded-full shadow-sm flex items-center justify-center transition-colors">✕</button>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" onclick="addQuestion()" class="w-full flex items-center justify-center gap-2 py-4 text-sm font-bold text-gray-600 bg-white border-2 border-dashed border-gray-300 rounded-2xl hover:bg-gray-50 hover:text-green-600 transition-all">
                    ＋ 設問を新しく追加する
                </button>
            </div>

            <div class="mt-12 flex justify-center">
                <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-extrabold rounded-2xl text-lg px-20 py-4 shadow-lg shadow-green-200 transition-all transform hover:-translate-y-1">
                    変更を保存する
                </button>
            </div>
        </form>
    </div>

    <script>
        let qCount = {{ count($survey->questions) }};

        function addQuestion() {
            // (※create.blade.phpのaddQuestionの中身と全く同じものが入ります)
            const container = document.getElementById('questions-container');
            const div = document.createElement('div');
            div.className = "bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative animate-fade-in group flex gap-4";
            
            div.innerHTML = `
                <div class="sort-handle flex items-center justify-center cursor-move text-gray-300 hover:text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="block mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">設問文</label>
                            <input type="text" name="questions[${qCount}][text]" required class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-2.5" placeholder="例：料理はいかがでしたか？">
                        </div>
                        <div class="w-full md:w-48">
                            <label class="block mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">回答形式</label>
                            <select name="questions[${qCount}][type]" onchange="toggleOptions(this, ${qCount})" class="bg-gray-50 border border-gray-200 text-sm rounded-xl focus:ring-green-500 block w-full p-2.5">
                                <option value="text">短いテキスト</option>
                                <option value="textarea">長いテキスト</option>
                                <option value="checkbox">複数選択（チェックボックス）</option>
                                <option value="radio">1つ選択（ラジオボタン）</option>
                            </select>
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="questions[${qCount}][is_required]" value="1" class="sr-only peer" checked>
                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                <span class="ms-2 text-xs font-bold text-gray-500">必須</span>
                            </label>
                        </div>
                    </div>
                    <div id="options-container-${qCount}" class="mt-4 p-4 bg-green-50 rounded-xl hidden">
                        <label class="block mb-2 text-xs font-bold text-brand uppercase">選択肢</label>
                        <div id="options-list-${qCount}" class="space-y-2 mb-3"></div>
                        <button type="button" onclick="addOption(${qCount})" class="text-xs font-bold text-green-600 bg-white border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">＋ 選択肢を追加</button>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove();" class="absolute -top-3 -right-3 bg-white border border-gray-200 text-gray-400 hover:text-white hover:bg-red-500 w-8 h-8 rounded-full shadow-sm flex items-center justify-center transition-colors">✕</button>
            `;
            container.appendChild(div);
            
            // 追加と同時に選択肢を1つセットしておく
            addOption(qCount);
            qCount++;
        }

        function addOption(index) {
            const list = document.getElementById(`options-list-${index}`);
            const div = document.createElement('div');
            div.className = "flex items-center gap-2 animate-fade-in";
            div.innerHTML = `
                <input type="text" name="questions[${index}][options][]" class="bg-white border border-green-200 text-sm rounded-lg focus:ring-green-500 block w-full p-2" placeholder="選択肢を入力">
                <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-red-500 px-2 font-bold">✕</button>
            `;
            list.appendChild(div);
        }

        function toggleOptions(select, index) {
            const optContainer = document.getElementById(`options-container-${index}`);
            if (select.value === 'checkbox' || select.value === 'radio') {
                optContainer.classList.remove('hidden');
                const list = document.getElementById(`options-list-${index}`);
                if (list.children.length === 0) addOption(index);
            } else {
                optContainer.classList.add('hidden');
            }
        }

        window.onload = function() {
            const container = document.getElementById('questions-container');
            new Sortable(container, {
                handle: '.sort-handle',
                animation: 150,
                ghostClass: 'opacity-50'
            });
        };
    </script>
</x-app-layout>