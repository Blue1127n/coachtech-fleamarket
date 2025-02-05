@extends('layouts.main')

@section('title', '決済確認画面')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
@endpush

@section('content')
<div class="payment-container">
    <h1>お支払い内容の確認</h1>

    <!-- 商品情報の表示 -->
    <div class="item-details">
        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
        <h2>{{ $item->name }}</h2>
        <p class="item-price">
            <span class="item-price-symbol">¥</span>
            <span class="item-price-value">{{ number_format($item->price) }}</span>
        </p>
    </div>

    <!-- 選択済みの支払い方法 -->
    <div class="payment-method">
        <h3>支払い方法</h3>
        <p>{{ $payment_method ?? '未選択' }}</p> <!-- 事前に選択済みの支払い方法を表示 -->
    </div>

    <!-- 購入ボタン -->
    <form action="{{ route('payment.checkout', ['item_id' => $item->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="payment_method" value="{{ $payment_method }}"> <!-- 支払い方法を引き継ぐ -->
        <button type="submit" class="payment-btn">購入する</button>
    </form>

    <!-- 戻るボタン -->
    <a href="{{ route('item.purchase', ['item_id' => $item->id]) }}" class="back-btn">戻る</a>
</div>
@endsection
