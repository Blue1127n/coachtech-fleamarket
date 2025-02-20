@extends('layouts.main')

@section('title', $item->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('content')
<div class="item-detail-container">

    <div class="item-detail-left">
        <div class="item-image">
            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
        </div>
    </div>

    <div class="item-detail-right">
        <div class="item-detail">
            <h1>{{ $item->name }}</h1>
            <p class="item-brand">ブランド: {{ $item->brand }}</p>
            <p class="item-price">
                <span class="item-price-symbol">¥</span>
                <span class="item-price-value">{{ number_format($item->price) }}</span>
                <span class="item-price-tax">（税込）</span>
            </p>

            <div class="item-actions">
                @if(auth()->check())
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
                    <a href="{{ route('login') }}" class="like-section">
                        <img src="{{ asset('storage/items/star-icon.png') }}" alt="いいねアイコン" class="like-icon">
                        <span id="like-count">{{ $item->likes->count() }}</span>
                    </a>
                @endif

                <div class="comment-section">
                    <img id="comment-icon" src="{{ asset('storage/items/ふきだしのアイコン.png') }}" alt="コメントアイコン">
                    <span id="comment-count">{{ $item->comments()->count() }}</span>
                </div>
            </div>

            @if(auth()->check())
                <a href="{{ route('item.purchase', ['item_id' => $item->id]) }}" class="purchase-btn">購入手続きへ</a>
            @else
                <a href="{{ route('login') }}" class="purchase-btn">購入手続きへ</a>
            @endif
        </div>

        <div class="item-description">
            <h2>商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>

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

        <div class="comments-section">
            <h2>コメント ({{ $item->comments()->count() }})</h2>
            <ul class="comments-list">
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
            </ul>

            <form id="comment-form" action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <p>商品へのコメント</p>
                <textarea name="content" id="comment-content">{{ old('content') }}</textarea>
                @if ($errors->has('content'))
                    <p class="error-message">{{ $errors->first('content') }}</p>
                @endif

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
        const commentContent = document.getElementById('comment-content');
        const commentList = document.querySelector('.comments-list');
        const commentCountElement = document.getElementById('comment-count');
        const commentHeading = document.querySelector('.comments-section h2');

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
                });
            });
        }

        if (commentForm) {
            commentForm.addEventListener('submit', function (event) {
                event.preventDefault();

                document.querySelector('.error-message')?.remove();

                if (!commentContent.value.trim()) {
                    displayErrorMessage('コメントを入力してください');
                    return;
                }

                fetch(commentForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        content: commentContent.value,
                    }),
                })
                .then(response => {
                    return response.json().then(data => {
                        if (!response.ok) {
                            throw { response, data };
                        }
                        return data;
                    });
                })
                .then(data => {
                    if (data.success) {
                        const newComment = document.createElement('li');
                        newComment.classList.add('comment');
                        newComment.innerHTML = `
                            ${data.comment.user.profile_image_url ? `<img src="${data.comment.user.profile_image_url}" alt="${data.comment.user.name}" class="user-profile-image">` : '<div class="user-profile-placeholder"></div>'}
                            <strong>${data.comment.user.name}</strong>
                            <p>${data.comment.content}</p>
                        `;
                        commentList.appendChild(newComment);

                        if (commentCountElement) {
                            let currentIconCount = parseInt(commentCountElement.textContent) || 0;
                            commentCountElement.textContent = currentIconCount + 1;
                        }

                        if (commentHeading) {
                            let currentCount = parseInt(commentHeading.textContent.match(/\d+/)[0]) || 0;
                            commentHeading.textContent = `コメント (${currentCount + 1})`;
                        }

                        commentForm.reset();
                    }
                })
                .catch(error => {
                    console.error("コメントエラー:", error);
                    if (error.data && error.data.errors) {
                        displayErrorMessage(error.data.errors.content ? error.data.errors.content[0] : "エラーが発生しました");
                    }
                });
            });
        }

        function displayErrorMessage(message) {
            const errorElement = document.createElement('p');
            errorElement.classList.add('error-message');
            errorElement.style.color = 'rgba(255, 85, 85, 1)';
            errorElement.style.fontSize = '14px';
            errorElement.textContent = message;
            commentContent.insertAdjacentElement('afterend', errorElement);
        }
    });
</script>
@endpush