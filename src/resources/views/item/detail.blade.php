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
            <p class="item-brand">ブランド: {{ $item->brand }}</p>
            <p class="item-price">
                <span class="item-price-symbol">¥</span>
                <span class="item-price-value">{{ number_format($item->price) }}</span>
                <span class="item-price-tax">（税込）</span>
            </p>

            <!-- いいねボタン -->
            <div class="item-actions">
                @if(auth()->check())
                <!-- ログイン済みのユーザー -->
                <form id="like-form" action="{{ route('item.like', ['item_id' => $item->id]) }}" method="POST">
                        @csrf
                        <div class="like-section">
                            <button type="submit" class="like-button">
                                <img src="{{ asset('storage/items/star-icon.png') }}" alt="いいねアイコン" class="like-icon {{ $isLiked ? 'liked' : '' }}">
                            </button>
                            <span id="like-count">{{ $item->likes->count() }}</span>
                        </div>
                    </form>
                @else
                    <!-- 未認証ユーザー -->
                    <a href="{{ route('login') }}" class="like-section">
                        <img src="{{ asset('storage/items/star-icon.png') }}" alt="いいねアイコン" class="like-icon">
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </a>
                @endif

                <!-- コメント数アイコン -->
                <div class="comment-section">
                    <img id="comment-icon" src="{{ asset('storage/items/ふきだしのアイコン.png') }}" alt="コメントアイコン">
                    <span id="comment-count">{{ $item->comments()->count() }}</span>
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
            <p class="categories-section">カテゴリ
                @foreach($item->categories as $category)
                    <span class="category-badge">{{ $category->name }}</span>
                @endforeach
            </p>
            <p class="condition-section">商品の状態
                <span class="condition-text">{{ $item->condition->condition ?? '未設定' }}</span>
            </p>
        </div>

        <!-- コメントセクション -->
        <div class="comments-section">
            <h2>コメント ({{ $item->comments()->count() }})</h2>
            <ul>
            @if($item->comments->isNotEmpty())
                @foreach ($item->comments as $comment)
                    <li class="comment">
                        @if ($comment->user->profile_image_url)
                            <img src="{{ $comment->user->profile_image_url }}" alt="{{ $comment->user->name }}" class="user-profile-image">
                        @else
                            <div class="user-profile-placeholder"></div>
                        @endif
                        <strong>{{ $comment->user->name }}</strong>
                        <p>{{ $comment->content }}</p>
                    </li>
                @endforeach
            @else
            <p>コメントはまだありません</p>
            @endif
            </ul>

        <!-- コメント投稿フォーム -->
        <form id="comment-form" action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="POST">
            @csrf
            <p>商品へのコメント</p>
            <textarea name="content" id="comment-content" required>{{ old('content') }}</textarea>
            @error('content')
            <p class="error-message">{{ $message }}</p>
            @enderror

            <button type="submit" class="comment-submit-btn">コメントを送信する</button>
        </form>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const likeForm = document.getElementById('like-form');
    const commentForm = document.getElementById('comment-form');
    const commentsCountElement = document.querySelector('.comments-section h2'); // コメント数の更新用

    console.log('like-form:', likeForm);
    console.log('comment-form:', commentForm);

    // like-form の処理
    if (likeForm) {
        likeForm.addEventListener('submit', function (event) {
            event.preventDefault();

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
                if (data) {
                    const likeIcon = likeForm.querySelector('.like-icon');
                    if (data.liked) {
                        likeIcon.classList.add('liked');
                    } else {
                        likeIcon.classList.remove('liked');
                    }
                    document.getElementById('like-count').textContent = data.likeCount;
                }
            })
            .catch(error => console.error('いいね処理エラー:', error));
        });
    } else {
        console.log('like-form が未ログイン状態または存在しないためスキップ');
    }

    // comment-form の処理
    if (commentForm) {
        commentForm.addEventListener('submit', function (event) {
            event.preventDefault();

        // ログイン確認
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
            alert('ログインが必要です。ログインしてください。');
            return;
        }

        fetch(commentForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfTokenMeta.content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                content: commentForm.content.value,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentsSection = document.querySelector('.comments-section ul');

                // プロフィール画像のURLを取得（ない場合は非表示）
                const profileImage = data.comment.user.profile_image_url
                    ? `<img src="${data.comment.user.profile_image_url}" alt="${data.comment.user.name}" class="user-profile-image">`
                    : '';

                // コメントのHTMLを生成
                const newComment = document.createElement('li');
                newComment.classList.add('comment');
                newComment.innerHTML = `
                    ${profileImage}
                    <strong>${data.comment.user.name}</strong>
                    <p>${data.comment.content}</p>
                `;

                // コメントリストに追加
                commentsSection.appendChild(newComment);

                // コメント数を更新
                const commentsCountElement = document.querySelector('.comments-section h2');
                const currentCount = parseInt(commentsCountElement.textContent.match(/\d+/)[0]) || 0;
                commentsCountElement.textContent = `コメント (${currentCount + 1})`;

                // フォームをリセット
                commentForm.reset();

                // エラーメッセージを消す
                document.querySelector('.error-message')?.remove();
            } else if (data.errors) {
                    // バリデーションエラーの表示
                let errorMessage = data.errors.content ? data.errors.content[0] : 'コメントの投稿に失敗しました';

                // すでにエラーメッセージがある場合は削除
                document.querySelector('.error-message')?.remove();

                // エラーメッセージを追加
                let errorElement = document.createElement('p');
                errorElement.classList.add('error-message');
                errorElement.style.color = 'red';
                errorElement.textContent = errorMessage;
                commentForm.appendChild(errorElement);
            }
        })
        .catch(error => {
                console.error('コメント処理エラー:', error);
                alert('通信エラーが発生しました。');
            });
        });
    }
});
</script>
@endpush
