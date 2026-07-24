<!DOCTYPE html>
<x-app-layout>
<head>
    <meta charset="UTF-8">
    <title>お知らせ編集</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
</head>

<div class="py-12">
<div class="plan_inner">
<body>
    <h1 class="art_tit"><span><img src="/img/plan.svg" alt="" class="h-10"></span>お知らせ編集</h1>
    <a href="{{ route('admin.notices.index') }}" class="re_btn">← 戻る</a>

    <div class="whi_box">
        <form action="{{ route('admin.notices.update', $notice->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div>
                <label>公開対象:</label>
                <select name="target_role" required>
                    <option value="all" {{ $notice->target_role == 'all' ? 'selected' : '' }}>全員</option>
                    <option value="agency" {{ $notice->target_role == 'agency' ? 'selected' : '' }}>代理店限定</option>
                    <option value="company" {{ $notice->target_role == 'company' ? 'selected' : '' }}>企業限定</option>
                </select>
            </div><br>

            <div>
                <label>カテゴリ:</label>
                <select name="notice_category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $notice->notice_category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div><br>

            <div>
                <label>タイトル:</label>
                <input type="text" name="title" value="{{ $notice->title }}" style="width: 100%;" required>
            </div><br>

            <div><textarea id="summernote" name="content">{!! $notice->content !!}</textarea></div><br>

            <button type="submit" class="sub_btn">更新する</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({ height: 300 });
        });
    </script>
</body>
</div>
</div>
            </x-app-layout>
</html>