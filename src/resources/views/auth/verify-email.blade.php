@extends('layouts.app')

@section('title', 'メールアドレス認証')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')
<div class="verify-email-container">
    <h1>メールアドレス認証</h1>
    <p>ご登録頂きましたメールアドレスに認証リンクを送信致しました<br>
    以下の認証リンクをクリックして、メールアドレスの認証を完了してください。</p>

    {{-- メッセージを表示 --}}
    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <p>認証メールを受け取っていない場合は、以下のボタンを押してください</p>

    {{-- 認証メール再送信フォーム --}}
    <form action="{{ route('verification.resend') }}" method="POST">
        @csrf
        <button type="submit" class="resend-button">認証メールを再送信</button>
    </form>
</div>
@endsection