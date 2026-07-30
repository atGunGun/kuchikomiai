<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coel</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; }
        .nav {/* text-align: right; *//* margin-bottom: 40px; */display: flex;justify-content: end;width: 100%;}
        .nav a { margin-left: 15px; text-decoration: none; font-weight: bold; }
        .hero {text-align: center;padding: 60px 0 0;/* background: #f8f9fa; */border-radius: 10px;margin-bottom: 40px;}
        .notice-section {border: 1px solid #f7f7f7;padding: 0px;border-radius: 8px;background: #fff;box-shadow: 0 0 14px #f1f1f1;overflow: hidden;}
        .notice-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .notice-tag { font-size: 0.8em; padding: 2px 6px; border-radius: 4px; background: #eee; margin-right: 10px; }
        /* welcome.blade.php の <style> 内に追記 */


.sp{
    display: none;
}

/* ページネーション全体のデザイン調整 */
.notice-section nav {
    margin-top: 20px;
}
/* 巨大な矢印アイコンを小さく制限する（最重要） */
.notice-section nav svg {
    width: 20px;
    height: 20px;
    display: inline-block;
}
/* 「Showing 1 to 10 of...」というテキストを小さくする、または隠す */
.notice-section nav div p {
    font-size: 0.8em;
    color: #666;
    margin-bottom: 10px;
}
/* 数字や矢印のリンクを横並びにする */
.notice-section nav span, 
.notice-section nav a {
    padding: 5px 10px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #007bff;
    border-radius: 4px;
    margin: 0 2px;
}
.notice-section nav span {
    background-color: #eee;
    color: #333;
}

.top_bg {
    background: url(/img/main_bg.jpg) top center no-repeat;
    height: 98vh;
    width: 100vw;
    max-width: 100vw;
    max-height:100vh;
    background-size: cover;
}

.inner{
    width: 90%;
    max-width: 800px;
    margin: 2vh auto;
}

.inner img{
    width: 100%;
}

.log_btn{
    color: #333; border: 1px solid #707070; display: block; width: 120px; text-align: center; padding: 10px 0; border-radius: 10px; background: #fff;
}

.new_btn{
    background: #0566F4; color: #fff; width: 120px; display: flex; justify-content: center; align-items: center; border-radius: 10px;
}

.nav a:hover{
    background: #288E52;
    color: #fff;
}

@media screen and (max-width: 768px) {
    .sp{
    display: block;
    max-width: 380px;
    margin: 0 auto;
   } 
   .pc{
    display: none;
   }
   .top_bg{
    height: auto;
    width: 100%;
    background: url(/img/main_bg_sp.jpg) top center no-repeat;
   }
}
    </style>
</head>
<body class="top_bg">
    <div class="inner">
        <div class="nav">
                @auth
                    <a href="{{ url('/dashboard') }}">ダッシュボード</a>
                @else
                <a href="./login" class="log_btn">ログイン</a>
                <a href="./register" class="new_btn">新規登録</a>
                @endauth
            </div>

            <div class="hero">
                <h1>
                    <img src="/img/main_img.png" alt="お客様は選ぶだけ。口コミをもっと簡単に。Coel" class="pc">
                    <img src="/img/main_img_sp.png" alt="お客様は選ぶだけ。口コミをもっと簡単に。Coel" class="sp">
                </h1>
            </div>

            <div class="notice-section">
            <div style="display: flex;justify-content: space-between;align-items: center;margin-bottom: 20px;padding: 20px;background: #E7F6F4;">
                    <h2 style="margin: 0; margin: 0; align-items: center; display: flex; line-height: 1;">
                    <span class="mr-2" style="width: 50px; margin:0 10px 0 0;"><img src="/img/top_aicon07.svg" alt="" class="h-10"></span> 
                        お知らせ
                    </h2>
                    
                    <form action="{{ route('top') }}" method="GET" id="perPageFormTop">
                        <label style="font-size: 0.8em; color: #666;">表示件数:</label>
                        <select name="per_page" onchange="document.getElementById('perPageFormTop').submit();" style="padding: 2px 5px; border-radius: 4px; border: 1px solid #ddd;">
                            @foreach([5, 10, 20, 50] as $num)
                                <option value="{{ $num }}" {{ $perPage == $num ? 'selected' : '' }}>{{ $num }}件</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="t_inner" style="padding: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee; text-align: left; font-size: 0.9em; color: #666;  padding: 20px;">
                                <th style="padding: 10px;">日付</th>
                                <th>カテゴリ</th>
                                <th>タイトル</th>
                                <th style="text-align: right;">詳細</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notices as $notice)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px 10px; font-size: 0.9em; color: #888;">{{ $notice->created_at->format('Y/m/d') }}</td>
                                    <td>
                                        <span style="font-size: 0.75em; padding: 3px 8px; border-radius: 4px; background: #e3f2fd; color: #1976d2;">
                                            {{ $notice->category->name ?? 'お知らせ' }}
                                        </span>
                                    </td>
                                    <td style="font-weight: bold; color: #333;">{{ $notice->title }}</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('notices.show', $notice->id) }}" style="text-decoration: none; color: #007bff; font-size: 0.9em;">詳細を見る →</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="padding: 20px; text-align: center; color: #999;">お知らせはありません。</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px;">
                    {{ $notices->links() }}
                </div>
            </div>
    </div>
</body>
</html>