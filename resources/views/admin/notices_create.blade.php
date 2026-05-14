<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規お知らせ作成</title>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body>
    <h1>新規お知らせ作成</h1>
    <a href="{{ route('admin.notices.index') }}">← 一覧へ戻る</a><hr>

    <form action="{{ route('admin.notices.store') }}" method="POST">
        @csrf
        <div>
            <label>公開対象:</label>
            <select name="target_role" required>
                <option value="all">全員（フロントページにも表示）</option>
                <option value="agency">代理店限定</option>
                <option value="company">企業限定</option>
            </select>
        </div><br>

        <div>
            <label>カテゴリ:</label>
            <select name="notice_category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div><br>

        <div>
            <label>タイトル:</label>
            <input type="text" name="title" style="width: 100%;" required>
        </div><br>

        <div>
            <label>本文:</label>
            <textarea id="summernote" name="content"></textarea>
        </div><br>

        <button type="submit" style="padding: 10px 20px;">この内容で公開する</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'お知らせの本文を入力してください...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']]
                ]
            });
        });
    </script>
</body>
</html>