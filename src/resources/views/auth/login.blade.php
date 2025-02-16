@extends('layouts.app')

@section('title', 'ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-container">
    <h2>ログイン</h2>

    <form id="login-form">
        @csrf

        <div class="form-group">
            <label for="email">ユーザー名 / メールアドレス</label>
            <input type="text" name="email" id="email" class="input-text" value="{{ old('email') }}">
            <div class="error-message" id="email-error"></div>
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="input-text">
            <div class="error-message" id="password-error"></div>
        </div>

        <button type="submit" class="btn-submit">ログインする</button>
    </form>

    <p><a href="{{ route('register') }}">会員登録はこちら</a></p>
</div>

@push('scripts')
<script>
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    // エラーメッセージの初期化
    document.getElementById('email-error').innerText = '';
    document.getElementById('password-error').innerText = '';

    fetch("{{ route('login') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
        },
        body: JSON.stringify({
            email: document.getElementById("email").value,
            password: document.getElementById("password").value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.errors) {
            // バリデーションエラーを表示
            if (data.errors.email) {
                document.getElementById('email-error').innerText = data.errors.email[0];
            }
            if (data.errors.password) {
                document.getElementById('password-error').innerText = data.errors.password[0];
            }
        } else {
            console.log("ログイン成功", data);
            window.location.href = "{{ route('products.index') }}"; // ログイン成功時のリダイレクト
        }
    })
    .catch(error => console.error("通信エラー", error));
});
</script>
@endpush
@endsection