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
            <p class="item-brand">ブランド：{{ $item->brand }}</p>
            <p class="item-price">
                <span class="item-price-symbol">¥</span>
                <span class="item-price-value">{{ number_format($item->price) }}</span>
                <span class="item-price-tax">（税込）</span>
            </p>

            <!-- いいねボタン -->
            <div class="item-actions">
                <form id="like-form" action="{{ route('item.like', ['item_id' => $item->id]) }}" method="POST">
                    @csrf
                    <div class="like-section">
                        <img src="{{ asset('storage/items/star-icon.png') }}" alt="いいねアイコン" class="like-icon {{ session('liked', $isLiked) ? 'liked' : '' }}">
                        <span id="like-count">{{ session('likeCount', 0) }}</span>
                    </div>
                </form>

                <!-- コメント数アイコン -->
                <div class="comment-section">
                    <img id="comment-icon" src="{{ asset('storage/items/ふきだしのアイコン.png') }}" alt="コメントアイコン">
                    <span id="comment-count">{{ $item->comments_count ?? 0 }}</span>
                </div>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const likeForm = document.getElementById('like-form');
    console.log('likeForm:', likeForm);

    if (likeForm) {
        likeForm.addEventListener('submit', function (event) {
            event.preventDefault();
            console.log('フォーム送信イベントがトリガーされました');

            // サーバーへのリクエストを送信
            fetch(likeForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({}),
            })
                .then(response => response.json())
                .then(data => {
                    console.log('サーバーからのレスポンス:', data);

                    // アイコンの状態を切り替え
                    const likeIcon = likeForm.querySelector('.like-icon');
                    if (data.liked) {
                        likeIcon.classList.add('liked');
                    } else {
                        likeIcon.classList.remove('liked');
                    }

                    // カウントを更新
                    document.getElementById('like-count').textContent = data.likeCount;
                })
                .catch(error => console.error('エラー:', error));
        });
    } else {
        console.error('like-form が見つかりません');
    }
});
</script>
@endpush
