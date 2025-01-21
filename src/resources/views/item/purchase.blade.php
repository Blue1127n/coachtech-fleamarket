@extends('layouts.main')

@section('title', '商品購入')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="purchase-container">
    <div class="purchase-details">
        <div class="item-info">
            <img src="{{ asset('storage/items/' . $item->image) }}" alt="商品画像" class="item-image">
            <div class="item-details">
                <h1 class="item-name">{{ $item->name }}</h1>
                <p class="item-price">¥ {{ number_format($item->price) }}</p>
            </div>
        </div>
        <div class="payment-method">
            <h2>支払い方法</h2>
            <form action="{{ route('item.purchase', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <select name="payment_method" id="payment_method" class="payment-select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="コンビニ払い">コンビニ払い</option>
                    <option value="カード払い">カード払い</option>
                </select>
                <button type="submit" class="purchase-btn">購入する</button>
            </form>
        </div>
        <div class="shipping-address">
            <h2>配送先</h2>
            <p>〒 {{ $user->postal_code }}</p>
            <p>{{ $user->address }}</p>
            <p>{{ $user->building }}</p>
            <a href="{{ route('item.changeAddress', ['item_id' => $item->id]) }}" class="change-address-link">変更する</a>
        </div>
    </div>
    <div class="summary">
        <h2>購入内容</h2>
        <div class="summary-box">
            <p>商品代金</p>
            <p>¥ {{ number_format($item->price) }}</p>
        </div>
        <div class="summary-box">
            <p>支払い方法</p>
            <p id="selected-method">未選択</p>
        </div>
        <button class="purchase-summary-btn">購入する</button>
    </div>
</div>
@endsection
