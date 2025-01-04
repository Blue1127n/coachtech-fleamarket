@extends('layouts.main')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')
<div class="product-container">
    <div class="product-tabs">
        <a href="{{ route('products.index') }}" class="tab active">おすすめ</a>
        <a href="{{ route('products.mylist') }}" class="tab">マイリスト</a>
    </div>
    <div class="product-grid">
        @foreach ($products as $product)
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ $product->image_url }}" alt="商品画像">
                    @if($product->is_sold)
                        <div class="sold-badge">SOLD</div>
                    @endif
                </div>
                <div class="product-name">{{ $product->name }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
