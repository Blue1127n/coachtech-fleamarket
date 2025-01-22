@extends('layouts.main')

@section('title', $item->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('content')
<div class="item-detail-container">
    <!-- 商品画像 -->
    <div class="item-detail-left">
        <div class="item-image">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
        </div>
    </div>

    <!-- 商品詳細 -->
    <div class="item-detail-right">
        <div class="item-detail">
            <h1>{{ $item->name }}</h1>
            <p>ブランド: {{ $item->brand }}</p>
            <p>価格: ¥{{ number_format($item->price) }}</p>

        <!-- いいねボタン -->
        <div class="item-actions">
            <form id="like-form" action="{{ route('item.like', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <div class="like-section">
                    <span id="like-icon">{{ session('liked', $isLiked) ? '★' : '☆' }}</span>
                    <span id="like-count">{{ session('likeCount', 0) }}</span>
                </div>
            </form>

            <!-- コメント数アイコン -->
            <div class="comment-section">
                <span id="comment-icon">💬</span>
                <span id="comment-count">{{ $item->comments_count ?? 0 }}</span>
            </div>
        </div>

        <!-- 購入ボタン -->
        <a href="{{ route('item.purchase', ['item_id' => $item->id]) }}" class="purchase-btn">購入手続きへ</a>

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
                    <span class="category-badge">{{ $category->name }}</span>
                @endforeach
            </p>
            <p>商品の状態: {{ $item->condition->condition ?? '未設定' }}</p>
        </div>

        <!-- コメントセクション -->
        <div class="comments-section">
            <h2>コメント ({{ $item->comments_count ?? 0 }})</h2>
            @if($item->comments->isNotEmpty())
            <ul>
                @foreach ($item->comments as $comment)
                    <li class="comment">
                        <strong>{{ $comment->user->name }}</strong>
                        <p>{{ $comment->content }}</p>
                        <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p>コメントはまだありません。</p>
         @endif
    </div>

            @auth
            <!-- コメント投稿フォーム -->
            <form action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <textarea name="content" placeholder="コメントを入力してください" required maxlength="255"></textarea>
                <button type="submit" class="comment-submit-btn">コメントを送信する</button>
            </form>
            @endauth

            @guest
            <p>コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> が必要です</p>
            @endguest
        </div>
    </div>
</div>
@endsection
