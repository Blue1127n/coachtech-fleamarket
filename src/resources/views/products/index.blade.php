@extends('layouts.main')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')
<div class="product-container">
    <div class="product-tabs">
        <a href="{{ route('products.index') }}"
            class="tab {{ request()->routeIs('products.index') ? 'active' : '' }}">おすすめ</a>
        <a href="{{ route('products.mylist') }}"
            class="tab {{ request()->routeIs('products.mylist') ? 'active' : '' }}">マイリスト</a>
    </div>

    <div class="product-grid">
        @if($products->isEmpty())
            @if($isMyList && !auth()->check())
                <p>ログインするとマイリストを表示できます</p>
            @else
                <p>商品がありません</p>
            @endif
        @else
            @foreach ($products as $product)
                <div class="product-card">
                <a href="{{ route('item.show', ['item_id' => $product->id]) }}">
                    <div class="product-image">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像">
                        @if($product->status_id == 5)
                            <div class="sold-badge">SOLD</div>
                        @endif
                    </div>
                    <div class="product-name">{{ $product->name }}</div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
