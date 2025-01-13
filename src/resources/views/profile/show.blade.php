@extends('layouts.main')

@section('title', 'プロフィール画面')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h2>{{ $user->name }}</h2>
        <a href="{{ route('mypage.profile') }}" class="btn">プロフィールを編集</a>
    </div>

    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items">
        @foreach($items as $item)
            <div class="item">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
            </div>
        @empty
            <p>表示する商品がありません</p>
        @endforelse
    </div>
</div>
@endsection