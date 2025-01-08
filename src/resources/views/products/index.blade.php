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
        @auth
        <a href="{{ route('products.mylist') }}"
            class="tab {{ request()->routeIs('products.mylist') ? 'active' : '' }}">マイリスト</a>
        @endauth
    </div>

    <div class="product-grid">
        @if($products->isEmpty())
            <p>商品がありません。</p>
        @else
            @foreach ($products as $product)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ $product->image_url }}" alt="商品画像">
                        @if($product->status->name === 'Sold') {{-- 販売済みならSOLDを表示 --}}
                            <div class="sold-badge">SOLD</div>
                        @endif
                    </div>
                    <div class="product-name">{{ $product->name }}</div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
