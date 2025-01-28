<?php $__env->startSection('title', $item->name); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/detail.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="item-detail-container">
    <!-- 商品画像 -->
    <div class="item-detail-left">
        <div class="item-image">
            <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>">
        </div>
    </div>

    <!-- 商品詳細 -->
    <div class="item-detail-right">
        <div class="item-detail">
            <h1><?php echo e($item->name); ?></h1>
            <p class="item-brand">ブランド: <?php echo e($item->brand); ?></p>
            <p class="item-price">
                <span class="item-price-symbol">¥</span>
                <span class="item-price-value"><?php echo e(number_format($item->price)); ?></span>
                <span class="item-price-tax">（税込）</span>
            </p>

            <!-- いいねボタン -->
            <div class="item-actions">
                <?php if(auth()->check()): ?>
                <!-- ログイン済みのユーザー -->
                <form id="like-form" action="<?php echo e(route('item.like', ['item_id' => $item->id])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="like-section">
                            <button type="submit" class="like-button">
                                <img src="<?php echo e(asset('storage/items/star-icon.png')); ?>" alt="いいねアイコン" class="like-icon <?php echo e($isLiked ? 'liked' : ''); ?>">
                            </button>
                            <span id="like-count"><?php echo e($item->likes->count()); ?></span>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- 未認証ユーザー -->
                    <a href="<?php echo e(route('login')); ?>" class="like-section">
                        <img src="<?php echo e(asset('storage/items/star-icon.png')); ?>" alt="いいねアイコン" class="like-icon">
                        <span id="like-count"><?php echo e($item->likes->count()); ?></span>
                    </a>
                <?php endif; ?>

                <!-- コメント数アイコン -->
                <div class="comment-section">
                    <img id="comment-icon" src="<?php echo e(asset('storage/items/ふきだしのアイコン.png')); ?>" alt="コメントアイコン">
                    <span id="comment-count"><?php echo e($item->comments()->count()); ?></span>
                </div>
            </div>

            <!-- 購入ボタン -->
            <a href="<?php echo e(route('item.purchase', ['item_id' => $item->id])); ?>" class="purchase-btn">購入手続きへ</a>
        </div>

        <!-- 商品説明 -->
        <div class="item-description">
            <h2>商品説明</h2>
            <p><?php echo e($item->description); ?></p>
        </div>

        <!-- 商品情報 -->
        <div class="item-info">
            <h2>商品情報</h2>
            <p class="categories-section">カテゴリ
                <?php $__currentLoopData = $item->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="category-badge"><?php echo e($category->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </p>
            <p class="condition-section">商品の状態
                <span class="condition-text"><?php echo e($item->condition->condition ?? '未設定'); ?></span>
            </p>
        </div>

        <!-- コメントセクション -->
        <div class="comments-section">
            <h2>コメント (<?php echo e($item->comments()->count()); ?>)</h2>
            <ul class="comments-list">
    <?php $__currentLoopData = $item->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="comment">
            <?php if($comment->user->profile_image_url): ?>
                <img src="<?php echo e($comment->user->profile_image_url); ?>" alt="<?php echo e($comment->user->name); ?>" class="user-profile-image">
            <?php else: ?>
                <div class="user-profile-placeholder"></div>
            <?php endif; ?>
            <strong><?php echo e($comment->user->name); ?></strong>
            <p><?php echo e($comment->content); ?></p>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>

        <!-- コメント投稿フォーム -->
        <form id="comment-form" action="<?php echo e(route('item.comment', ['item_id' => $item->id])); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <p>商品へのコメント</p>
            <textarea name="content" id="comment-content" required><?php echo e(old('content')); ?></textarea>
            <?php if($errors->has('content')): ?>
        <p class="error-message" style="color: red;"><?php echo e($errors->first('content')); ?></p>
    <?php endif; ?>

            <button type="submit" class="comment-submit-btn">コメントを送信する</button>
        </form>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const likeForm = document.getElementById('like-form');
    const commentForm = document.getElementById('comment-form');
    const commentContent = document.getElementById('comment-content');
    const commentList = document.querySelector('.comments-list'); // コメント一覧
    const commentCountElement = document.getElementById('comment-count'); // コメントアイコンの数
    const commentHeading = document.querySelector('.comments-section h2'); // 「コメント (0)」

    // いいね機能
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
    }

    // コメント機能
    if (commentForm) {
        commentForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // **未入力チェック**
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
                if (response.status === 401) {
                    return response.json().then(data => {
                        window.location.href = data.redirect; // 未ログインならログイン画面へ遷移
                    });
                }
                if (response.status === 422) {
                    return response.json().then(data => { throw data.errors; });
                }
                return response.json();
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
                    document.querySelector('.error-message')?.remove();
                }
            })
            .catch(errors => {
                console.error('バリデーションエラー:', errors);

                let errorMessage = 'コメントの投稿に失敗しました';
                if (errors.content) {
                    errorMessage = errors.content[0];
                }

                displayErrorMessage(errorMessage);
            });
        });
    }

    // **エラーメッセージを表示する関数**
    function displayErrorMessage(message) {
        document.querySelector('.error-message')?.remove();

        let errorElement = document.createElement('p');
        errorElement.classList.add('error-message');
        errorElement.style.color = 'red';
        errorElement.textContent = message;
        commentForm.appendChild(errorElement);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/detail.blade.php ENDPATH**/ ?>