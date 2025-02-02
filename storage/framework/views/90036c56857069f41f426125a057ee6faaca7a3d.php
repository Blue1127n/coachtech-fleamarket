<?php $__env->startSection('title', '決済確認画面'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/payment.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="payment-container">
    <h1>お支払い内容の確認</h1>

    <!-- 商品情報の表示 -->
    <div class="item-details">
        <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="item-image">
        <h2><?php echo e($item->name); ?></h2>
        <p class="item-price">
            <span class="item-price-symbol">¥</span>
            <span class="item-price-value"><?php echo e(number_format($item->price)); ?></span>
        </p>
    </div>

    <!-- 送付先情報の表示 -->
    <div class="shipping-info">
        <h3>送付先情報</h3>
        <p>〒<?php echo e($shipping['postal_code']); ?></p>
        <p><?php echo e($shipping['address']); ?></p>
        <?php if(!empty($shipping['building'])): ?>
            <p><?php echo e($shipping['building']); ?></p>
        <?php endif; ?>
    </div>

    <!-- 選択済みの支払い方法 -->
    <div class="payment-method">
        <h3>支払い方法</h3>
        <p><?php echo e($payment_method ?? '未選択'); ?></p> <!-- 事前に選択済みの支払い方法を表示 -->
    </div>

    <!-- 購入ボタン -->
    <form action="<?php echo e(route('payment.checkout', ['item_id' => $item->id])); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="payment_method" value="<?php echo e($payment_method); ?>"> <!-- 支払い方法を引き継ぐ -->
        <button type="submit" class="payment-btn">購入する</button>
    </form>

    <!-- 戻るボタン -->
    <a href="<?php echo e(route('item.purchase', ['item_id' => $item->id])); ?>" class="back-btn">戻る</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/payment/payment.blade.php ENDPATH**/ ?>