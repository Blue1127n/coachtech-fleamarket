@extends('layouts.main')

@section('title', $item->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('content')
<div class="item-detail-container">
    <div class="item-detail">
        <!-- 商品情報 -->
        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
        <h1>{{ $item->name }}</h1>
        <p>ブランド: {{ $item->brand }}</p>
        <p>価格: ¥{{ number_format($item->price) }}</p>

        <div class="item-actions">
            <!-- いいねボタン -->
            @php
                $liked = session('liked', $isLiked);
                $likeCount = session('likeCount', $item->likes_count);
            @endphp
            <form id="like-form" action="{{ route('item.like', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <button type="submit" id="like-button">
                    <span id="like-icon">{{ $liked ? '★' : '' }}</span>
                    <span id="like-count">{{ $likeCount }}</span>
                </button>
            </form>

            <!-- コメント投稿フォーム -->
            @auth
            <form id="comment-form" action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <textarea name="content" id="comment-content" placeholder="コメントを入力してください" required></textarea>
                <button type="submit">送信</button>
            </form>
            @endauth

            @guest
            <p>コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> が必要です。</p>
            @endguest
        </div>

        <!-- コメント一覧 -->
        <div id="comments-section">
            @foreach ($item->comments as $comment)
                <div class="comment">
                    <strong>{{ $comment->user->name }}</strong>
                    <p>{{ $comment->content }}</p>
                    <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                </div>
            @endforeach
        </div>

        <!-- 購入ボタン -->
        <a href="{{ route('item.purchase', ['item_id' => $item->id]) }}" class="purchase-btn">購入手続きへ</a>
    </div>

    <!-- 商品説明 -->
    <div class="item-description">
        <h2>商品説明</h2>
        <p>{{ $item->description }}</p>
    </div>

    <!-- 商品情報 -->
    <div class="item-info">
        <h2>商品情報</h2>
        <p>カテゴリ:
            @foreach($item->categories as $category)
                <span>{{ $category->name }}</span>
            @endforeach
        </p>
        <p>商品の状態: {{ $item->condition->condition }}</p>
    </div>
</div>
@endsection
