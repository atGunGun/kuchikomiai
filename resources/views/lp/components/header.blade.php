<header>
    <div class="hLogo">
        <a href="{{ url('/') }}">
            <h1 class="title">
                <img src="{{ asset('lp/img/common/logo.svg') }}" alt="Coel">
            </h1>
        </a>
    </div>

    <ul class="headerUl">
        <li class="menuBtn sp">
            <div class="menu-trigger">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <nav class="gnav">
                <div class="gnav__wrap">
                    <ul class="gnav__menu f700">
                        <li class="gnav__menu__item">
                            <a href="{{ url('/') }}">TOP</a>
                        </li>

                        <li class="gnav__menu__item">
                            <a href="{{ url('/contact') }}">お問い合わせ</a>
                        </li>

                        <li class="gnav__menu__item2">
                            <div class="hd_link">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="hd_link_btn1 bgwh">
                                    ダッシュボード
                                </a>
                            @else
                                <a href="{{ url('/login') }}" class="hd_link_btn1 bgwh">
                                    ログイン
                                </a>

                                <a href="{{ url('/register') }}" class="hd_link_btn1 bgblu white">
                                    新規登録
                                </a>
                            @endauth
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </li>
    </ul>


    <div class="pc_nav">
        <ul class="nav_list1">
            <li><a href="{{ url('/contact') }}">お問い合わせ</a></li>
        </ul>

        <div class="hd_link">
            @auth
                <a href="{{ url('/dashboard') }}" class="hd_link_btn1 bgwh">ダッシュボード</a>
            @else
                <a href="{{ url('/login') }}" class="hd_link_btn1 bgwh">
                    ログイン
                </a>
                <a href="{{ url('/register') }}" class="hd_link_btn1 bgblu white">
                    新規登録
                </a>
            @endauth
        </div>
    </div>
</header>