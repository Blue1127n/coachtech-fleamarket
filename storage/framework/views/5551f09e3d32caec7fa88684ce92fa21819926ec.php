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
            <p class="item-brand">ブランド：<?php echo e($item->brand); ?></p>
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
            <p>カテゴリ:
                <?php $__currentLoopData = $item->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="category-badge"><?php echo e($category->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </p>
            <p>商品の状態: <?php echo e($item->condition->condition ?? '未設定'); ?></p>
        </div>

        <!-- コメントセクション -->
        <div class="comments-section">
            <h2>コメント (<?php echo e($item->comments_count ?? 0); ?>)</h2>
                <?php if($item->comments->isNotEmpty()): ?>
                <ul>
                    <?php $__currentLoopData = $item->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="comment">
                            <strong><?php echo e($comment->user->name); ?></strong>
                            <p><?php echo e($comment->content); ?></p>
                            <span><?php echo e($comment->created_at->format('Y-m-d H:i')); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                <p>コメントはまだありません。</p>
            <?php endif; ?>
        </div>

            <?php if(auth()->guard()->check()): ?>
            <!-- コメント投稿フォーム -->
            <form action="<?php echo e(route('item.comment', ['item_id' => $item->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="content" placeholder="コメントを入力してください" required maxlength="255"></textarea>
                <button type="submit" class="comment-submit-btn">コメントを送信する</button>
            </form>
            <?php endif; ?>

            <?php if(auth()->guard()->guest()): ?>
            <p>コメントを投稿するには <a href="<?php echo e(route('login')); ?>">ログイン</a> が必要です</p>
            <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const likeForm = document.getElementById('like-form');

    if (!likeForm) {
        console.error('like-form が見つかりません');
        return;
    }

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
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/detail.blade.php ENDPATH**/ ?>