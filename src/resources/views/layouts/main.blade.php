<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'coachtech')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('styles')
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <header class="main-header">
            <div class="logo">
                <img src="{{ asset('storage/items/logo.svg') }}" alt="Logo">
            </div>
            <div class="header__search">
                <input type="text" placeholder="なにをお探しですか？" class="search-box">
            </div>
            <div class="header__menu">
                <a href="{{ route('login') }}">ログイン</a>
                <a href="{{ route('mypage') }}">マイページ</a>
                <a href="{{ route('sell') }}" class="header__sell-btn">出品</a>
            </div>
        </header>

        <!-- メインコンテンツ -->
        <main>
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
