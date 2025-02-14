@extends('layouts.app')

@section('title', 'ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-container">
    <h2>ログイン</h2>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">ユーザー名 / メールアドレス</label>
            <input type="text" name="email" id="email" class="input-text" value="{{ old('email') }}">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="input-text">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">ログインする</button>
    </form>

    <p><a href="{{ route('register') }}">会員登録はこちら</a></p>
</div>
@endsection
