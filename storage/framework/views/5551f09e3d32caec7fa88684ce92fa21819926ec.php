<?php $__env->startSection('title', $item->name); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/detail.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="item-detail-container">
    <div class="item-detail">
        <!-- 商品情報 -->
        <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>">
        <h1><?php echo e($item->name); ?></h1>
        <p>ブランド: <?php echo e($item->brand); ?></p>
        <p>価格: ¥<?php echo e(number_format($item->price)); ?></p>

        <div class="item-actions">
            <!-- いいねボタン -->
            <?php
                $liked = session('liked', $isLiked);
                $likeCount = session('likeCount', $item->likes_count);
            ?>
            <form id="like-form" action="<?php echo e(route('item.like', ['item_id' => $item->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" id="like-button">
                    <span id="like-icon"><?php echo e($liked ? '★' : '☆'); ?></span>
                    <span id="like-count"><?php echo e($likeCount); ?></span>
                </button>
            </form>

            <!-- コメント投稿フォーム -->
            <?php if(auth()->guard()->check()): ?>
            <form id="comment-form" action="<?php echo e(route('item.comment', ['item_id' => $item->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <textarea name="content" id="comment-content" placeholder="コメントを入力してください" required></textarea>
                <button type="submit">送信</button>
            </form>
            <?php endif; ?>

            <?php if(auth()->guard()->guest()): ?>
            <p>コメントを投稿するには <a href="<?php echo e(route('login')); ?>">ログイン</a> が必要です。</p>
            <?php endif; ?>
        </div>

        <!-- コメント一覧 -->
        <div id="comments-section">
            <?php $__currentLoopData = $item->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="comment">
                    <strong><?php echo e($comment->user->name); ?></strong>
                    <p><?php echo e($comment->content); ?></p>
                    <span><?php echo e($comment->created_at->format('Y-m-d H:i')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <span><?php echo e($category->name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
        <p>商品の状態: <?php echo e($item->condition->condition); ?></p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/detail.blade.php ENDPATH**/ ?>