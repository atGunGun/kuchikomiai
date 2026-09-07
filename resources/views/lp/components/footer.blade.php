<footer>
    <div class="inner">
        <div class="flex">
            <div class="left">
                <p class="logo">
                    <a href="{{ url('/register') }}">
                        <img src="{{ asset('lp/img/common/logo.svg') }}" alt="Coel">
                    </a>
                </p>
            </div>

            <div class="right">
                <ul class="ft_list1">
                    <li><a href="{{ url('/contact') }}">お問い合わせ</a></li>
                </ul>

                <ul class="btn_list1">
                    @auth
                    <li>
                        <a href="{{ url('/dashboard') }}" class="bgwh">
                            ダッシュボード
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{ url('/login') }}" class="bgwh">
                            ログイン
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/register') }}" class="bgblu white">
                            新規登録
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>

        <p class="copyright">
            © Coel
        </p>
    </div>
</footer>