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

        <!-- いいねアイコンとコメントアイコン -->
        <div class="item-actions">
            <button class="like-btn" data-item-id="{{ $item->id }}">
                <i class="fa {{ $item->liked_by_user ? 'fa-heart' : 'fa-heart-o' }}"></i>
                <span>{{ $item->likes_count }}</span>
            </button>
            <span class="comment-count">
                <i class="fa fa-comment"></i>
                <span>{{ $item->comments_count }}</span>
            </span>
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

    <!-- コメントセクション -->
    <div class="comments-section">
        <h2>コメント ({{ $item->comments_count }})</h2>
        <ul class="comments-list">
            @foreach ($item->comments as $comment)
                <li>
                    <strong>{{ $comment->user->name }}</strong>
                    <p>{{ $comment->content }}</p>
                    <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                </li>
            @endforeach
        </ul>

        @auth
        <!-- コメント投稿フォーム -->
        <form action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            <textarea name="content" placeholder="商品へのコメントを入力してください" required maxlength="255"></textarea>
            <button type="submit" class="comment-submit-btn">コメントを送信する</button>
        </form>
        @endauth

        @guest
        <p>コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> が必要です。</p>
        @endguest
    </div>
</div>
@endsection
