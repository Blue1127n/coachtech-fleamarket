<?php $__env->startSection('title', '商品一覧'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/index.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="product-container">
    <div class="product-tabs">
        <a href="<?php echo e(route('products.index')); ?>"
            class="tab <?php echo e(request()->routeIs('products.index') ? 'active' : ''); ?>">おすすめ</a>
        <a href="<?php echo e(route('products.mylist')); ?>"
            class="tab <?php echo e(request()->routeIs('products.mylist') ? 'active' : ''); ?>">マイリスト</a>
    </div>

    <div class="product-grid">
        <?php if($products->isEmpty()): ?>
            <?php if($isMyList && !auth()->check()): ?>
                <p>ログインするとマイリストを表示できます</p>
            <?php else: ?>
                <p>商品がありません</p>
            <?php endif; ?>
        <?php else: ?>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo e('storage/' . $product->image); ?>" alt="商品画像">
                        <?php if($product->status_id !== 1): ?> 
                            <div class="sold-badge">SOLD</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-name"><?php echo e($product->name); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/products/index.blade.php ENDPATH**/ ?>