<input type="text" name="title" placeholder="アンケートタイトル">
<textarea name="description">アンケートの冒頭説明文（本文）</textarea>

<hr>

<div id="question-list">
    </div>

<button type="button" onclick="addQuestion()">＋ 設問を追加する</button>

<script>
let qCount = 0;
function addQuestion() {
    if (qCount >= 50) return alert('最大50個までです');
    
    const html = `
        <div class="question-item" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <input type="hidden" name="questions[${qCount}][sort_order]" value="${qCount}">
            <input type="text" name="questions[${qCount}][text]" placeholder="質問文">
            <select name="questions[${qCount}][type]">
                <option value="text">テキスト</option>
                <option value="textarea">テキストエリア</option>
                <option value="checkbox">チェックボックス</option>
            </select>
            <label><input type="checkbox" name="questions[${qCount}][is_required]"> 必須</label>
            <button type="button" onclick="this.parentElement.remove()">削除</button>
        </div>
    `;
    document.getElementById('question-list').insertAdjacentHTML('beforeend', html);
    qCount++;
}
</script>