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
                    <option value="" class="default-option" disabled selected hidden>選択してください</option>
                    <option value="コンビニ払い" class="convenience-option">コンビニ払い</option>
                    <option value="カード払い" class="card-option">カード払い</option>
                </select>
            </form>
        </div>

        <div class="shipping-address">
            <h2>配送先</h2>
            <div class="shipping-content">
                <div class="shipping-info">
                    <?php if(auth()->check()): ?>
                        <p>〒 <?php echo e(auth()->user()->postal_code ?? '未登録'); ?></p>
                        <p><?php echo e(auth()->user()->address ?? '未登録'); ?></p>
                        <p><?php echo e(auth()->user()->building ?? '未登録'); ?></p>
                    <?php else: ?>
                        <p>配送先情報がありません。</p>
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('item.changeAddress', ['item_id' => $item->id])); ?>" class="change-address-link">変更する</a>
            </div>
        </div>
    </div>

    <div class="summary-container">
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td>商品代金</td>
                    <td>¥ <?php echo e(number_format($item->price)); ?></td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-method">未選択</td>
                </tr>
            </table>
        </div>
        <div class="summary-button">
            <button class="purchase-summary-btn">購入する</button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("payment_method");

    select.addEventListener("change", function () {
        // 選択されたオプションに✓をつける（プルダウンを開いた時のみ）
        for (let option of select.options) {
            if (option.value === select.value) {
                option.textContent = `✓ ${option.value}`;
            } else {
                option.textContent = option.value;
            }
        }

        // 選択後（プルダウンを閉じた時）は✓を消す
        setTimeout(() => {
            for (let option of select.options) {
                option.textContent = option.value;
            }
        }, 100);
    });
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/purchase.blade.php ENDPATH**/ ?>