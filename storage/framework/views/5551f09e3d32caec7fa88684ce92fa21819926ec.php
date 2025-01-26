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
                                <img src="<?php echo e(asset('storage/items/star-icon.png')); ?>" alt="いいねアイコン" class="like-icon <?php echo e(session('liked', $isLiked) ? 'liked' : ''); ?>">
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
                    <span id="comment-count"><?php echo e($item->comments_count ?? 0); ?></span>
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
            <h2>コメント (<?php echo e($item->comments_count ?? 0); ?>)</h2>
            <?php if($item->comments->isNotEmpty()): ?>
            <ul>
                <?php $__currentLoopData = $item->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="comment">
                        <img src="<?php echo e($comment->user->profile_image_url); ?>" alt="<?php echo e($comment->user->name); ?>" class="user-profile-image">
                        <strong><?php echo e($comment->user->name); ?></strong>
                        <p><?php echo e($comment->content); ?></p>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p>コメントはまだありません。</p>
            <?php endif; ?>
        </div>

        <!-- コメント投稿フォーム -->
        <form action="<?php echo e(route('item.comment', ['item_id' => $item->id])); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <p>商品へのコメント</p>
            <textarea name="content" required maxlength="255"></textarea>
            <button type="submit" class="comment-submit-btn">コメントを送信する</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const likeForm = document.getElementById('like-form');

    if (!likeForm) {
        likeForm.addEventListener('submit', function (event) {
            event.preventDefault(); // デフォルトのフォーム送信を防止

            fetch(likeForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                if (response.redirected) {
                    // ログイン画面へのリダイレクトが発生した場合
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data) {
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
                }
            })
            .catch(error => {
                console.error('Fetchエラー:', error);
                alert('いいねに失敗しました。再度お試しください。');
            });
        });
    } else {
            console.error('like-form が見つかりません');
        }

        const commentForm = document.getElementById('comment-form');

    if (commentForm) {
        commentForm.addEventListener('submit', function (event) {
            <?php if(auth()->guard()->guest()): ?>
           // ログインしていない場合、フォーム送信を防止してリダイレクト
            event.preventDefault();
            alert('コメントを投稿するにはログインが必要です。ログイン画面に移動します。');
            window.location.href = "<?php echo e(route('login')); ?>";
            <?php endif; ?>

           // ログイン済みの場合
            <?php if(auth()->guard()->check()): ?>
            event.preventDefault(); // デフォルトのフォーム送信を防止

            // コメント投稿リクエストを送信
            fetch(commentForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    content: commentForm.content.value,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('コメント送信成功:', data);

                    // コメントリストを更新する例 (必要に応じてカスタマイズ)
                    const commentsSection = document.querySelector('.comments-section ul');
                    const newComment = document.createElement('li');
                    newComment.classList.add('comment');
                    newComment.innerHTML = `
                        <strong>${data.comment.user.name}</strong>
                        <p>${data.comment.content}</p>
                        <span>${data.comment.created_at}</span>
                    `;
                    commentsSection.appendChild(newComment);

                    // フォームをクリア
                    commentForm.reset();
                } else {
                    console.error('コメント送信失敗:', data);
                    alert('コメント送信に失敗しました。再度お試しください。');
                }
            })
            .catch(error => {
                console.error('Fetchエラー:', error);
                alert('コメント送信に失敗しました。再度お試しください。');
            });
            <?php endif; ?>
        });
    } else {
        console.error('comment-form が見つかりません');
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/detail.blade.php ENDPATH**/ ?>