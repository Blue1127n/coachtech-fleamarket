<?php $__env->startSection('title', '商品購入'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/purchase.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="purchase-container">
    <div class="purchase-details">
        <div class="item-info">
            <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="item-image">
            <div class="item-details">
                <h1 class="item-name"><?php echo e($item->name); ?></h1>
                <p class="item-price">
                    <span class="item-price-symbol">¥</span>
                    <span class="item-price-value"><?php echo e(number_format($item->price)); ?></span>
                </p>
            </div>
        </div>

        <div class="payment-method">
            <h2>支払い方法</h2>
            <form action="<?php echo e(route('item.purchase', ['item_id' => $item->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <select name="payment_method" id="payment_method" class="payment-select">
                    <option value="" disabled selected>選択してください</option>
                    <option value="コンビニ払い">コンビニ払い</option>
                    <option value="カード払い">カード払い</option>
                </select>
            </form>
        </div>
        <div class="shipping-address">
            <h2>配送先</h2>
            <?php if(auth()->check()): ?>
                <p>〒 <?php echo e(auth()->user()->postal_code ?? '未登録'); ?></p>
                <p><?php echo e(auth()->user()->address ?? '未登録'); ?></p>
                <p><?php echo e(auth()->user()->building ?? '未登録'); ?></p>
            <?php else: ?>
                <p>配送先情報がありません。</p>
            <?php endif; ?>
            <a href="<?php echo e(route('item.changeAddress', ['item_id' => $item->id])); ?>" class="change-address-link">変更する</a>
        </div>
    </div>
    <div class="summary">
        <div class="summary-box">
            <p>商品代金</p>
            <p>¥ <?php echo e(number_format($item->price)); ?></p>
        </div>
        <div class="summary-box">
            <p>支払い方法</p>
            <p id="selected-method">未選択</p>
        </div>
        <button class="purchase-summary-btn">購入する</button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/purchase.blade.php ENDPATH**/ ?>