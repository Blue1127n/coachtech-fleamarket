@extends('layouts.main')

@section('title', 'プロフィール画面')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image">
            <img src="{{ session('profile_image_temp') ?: (auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '') }}" alt="プロフィール画像">
        </div>

        <div class="profile-info">
            <h2>{{ $user->name }}</h2>
        </div>
        <div class="profile-btn">
            <a href="{{ route('mypage.profile') }}" class="btn">プロフィールを編集</a>
            </div>
    </div>

    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items">
        @if($items->isNotEmpty())
            @foreach($items as $item)
                <div class="item">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                    <p>{{ $item->name }}</p>
                </div>
            @endforeach
        @else
            <p>表示する商品がありません</p>
        @endif
    </div>
</div>
@endsection