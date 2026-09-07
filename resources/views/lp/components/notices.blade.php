<style>
.notice-section {
    border: 1px solid #f7f7f7;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 0 14px #f1f1f1;
    overflow: hidden;
}

.notice-section nav {
    margin-top:20px;
}

.notice-section nav svg {
    width:20px;
    height:20px;
}

.notice-section nav div p {
    font-size:0.8em;
    color:#666;
}
</style>

<div class="notice-section">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding:20px;background:#E7F6F4;">

        <h2 style="margin:0;display:flex;align-items:center;line-height:1;">
            <span style="width:50px;margin-right:10px;">
                <img src="{{ asset('lp/img/top_aicon07.svg') }}" alt="">
            </span>
            お知らせ
        </h2>

        <form action="{{ route('top') }}" method="GET">
            <label style="font-size:0.8em;color:#666;">
                表示件数:
            </label>

            <select name="per_page"
                onchange="this.form.submit();"
                style="padding:2px 5px;border-radius:4px;border:1px solid #ddd;">

                @foreach([5,10,20,50] as $num)
                    <option value="{{ $num }}" {{ $perPage == $num ? 'selected' : '' }}>
                        {{ $num }}件
                    </option>
                @endforeach

            </select>
        </form>

    </div>


    <div style="padding:20px;">

        <table style="width:100%;border-collapse:collapse;">

            <thead>
                <tr style="border-bottom:2px solid #eee;color:#666;">
                    <th style="padding:10px;text-align:left;">日付</th>
                    <th>カテゴリ</th>
                    <th>タイトル</th>
                    <th style="text-align:right;">詳細</th>
                </tr>
            </thead>


            <tbody>

            @forelse($notices as $notice)

                <tr style="border-bottom:1px solid #eee;">

                    <td style="padding:12px 10px;color:#888;">
                        {{ $notice->created_at->format('Y/m/d') }}
                    </td>


                    <td>
                        <span style="font-size:.75em;padding:3px 8px;border-radius:4px;background:#e3f2fd;color:#1976d2;">
                            {{ $notice->category->name ?? 'お知らせ' }}
                        </span>
                    </td>


                    <td style="font-weight:bold;">
                        {{ $notice->title }}
                    </td>


                    <td style="text-align:right;">
                        <a href="{{ route('notices.show',$notice->id) }}">
                            詳細を見る →
                        </a>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="4" style="padding:20px;text-align:center;color:#999;">
                        お知らせはありません。
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <div style="margin-top:20px;">
        {{ $notices->links() }}
    </div>

</div>