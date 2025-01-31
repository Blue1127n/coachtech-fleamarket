<?php $__env->startSection('title', 'プロフィール画面'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/show.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-image">
            <img src="<?php echo e(session('profile_image_temp') ?: (auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '')); ?>" alt="">
        </div>

        <div class="profile-info">
            <h2><?php echo e($user->name); ?></h2>
        </div>
        <div class="profile-btn">
            <a href="<?php echo e(route('mypage.profile')); ?>" class="btn">プロフィールを編集</a>
        </div>
    </div>

    <div class="tabs">
        <a href="<?php echo e(route('mypage', ['page' => 'sell'])); ?>" class="<?php echo e($page === 'sell' ? 'active' : ''); ?>">出品した商品</a>
        <a href="<?php echo e(route('mypage', ['page' => 'buy'])); ?>" class="<?php echo e($page === 'buy' ? 'active' : ''); ?>">購入した商品</a>
    </div>

    <div class="items">
        <?php if($items->isNotEmpty()): ?>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="item">
                    <div class="product-image">
                        <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>">
                        <?php if($item->status_id == 5): ?> <!-- 5 = 売り切れ -->
                            <div class="sold-badge">SOLD</div>
                        <?php endif; ?>
                    </div>
                    <p><?php echo e($item->name); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p><?php echo e($page === 'sell' ? '出品した商品がありません' : '購入した商品がありません'); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/profile/show.blade.php ENDPATH**/ ?>