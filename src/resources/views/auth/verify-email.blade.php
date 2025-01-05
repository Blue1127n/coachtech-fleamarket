@extends('layouts.app')

@section('title', 'メールアドレス認証')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endpush

@section('content')
<div class="verify-email-container">
    <h1>メールアドレス認証</h1>
    <p>ご登録頂きましたメールアドレスに認証リンクを送信致しました<br>
    認証リンクをクリックして、メールアドレスの認証を完了してください</p>
    <p>認証メールを受け取らない場合は、<a href="{{ route('verification.resend') }}">こちら</a>をクリックして再送信してください。</p>
</div>
@endsection
