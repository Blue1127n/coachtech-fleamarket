<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'coachtech')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('styles')
</head>
<body>
    <div class="container">
        <header class="main-header">
            <div class="header__logo">
                <img src="{{ asset('storage/items/logo.svg') }}" alt="Logo">
            </div>
            <div class="header__search">
                <form action="{{ route('products.index') }}" method="GET">
                    <input type="text" name="search" placeholder="なにをお探しですか？" class="search-box" value="{{ request('search') }}">
                </form>
            </div>
            <div class="header__menu">
                @guest
                    <a href="{{ route('login') }}">ログイン</a>
                @endguest

                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">ログアウト</button>
                    </form>
                @endauth

                <a href="{{ route('mypage') }}">マイページ</a>
                <a href="{{ route('item.create') }}" class="header__sell-btn">出品</a>
            </div>
        </header>

        <main>
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
