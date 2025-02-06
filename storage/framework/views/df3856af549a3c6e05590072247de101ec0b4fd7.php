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

        <?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="payment-method">
    <h2>支払い方法</h2>
    <form action="<?php echo e(route('item.processPurchase', ['item_id' => $item->id])); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="custom-payment-select">
            <div class="selected-option" id="selectedPayment">選択してください</div>
            <div class="dropdown-options" id="paymentDropdown">
                <div class="dropdown-option" data-value="コンビニ払い">
                    <span class="check-icon"></span>コンビニ払い
                </div>
                <div class="dropdown-option" data-value="カード支払い">
                    <span class="check-icon"></span>カード支払い
                </div>
            </div>
            <input type="hidden" name="payment_method" id="paymentInput">
        </div>
        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="error-message"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </form>
</div>

        <div class="shipping-address">
            <h2>配送先</h2>
            <div class="shipping-content">
                <div class="shipping-info">
                    <p>〒 <?php echo e(preg_replace('/(\d{3})(\d{4})/', '$1-$2', $postalCode)); ?></p>
                    <p><?php echo e($address); ?></p>
                    <?php if(!empty($building)): ?>
                        <p><?php echo e($building); ?></p>
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
                    <td class="price">
                        <span class="price-symbol">¥</span>
                        <span class="price-value"><?php echo e(number_format($item->price)); ?></span>
                    </td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-method">未選択</td>
                </tr>
            </table>
        </div>

        <div class="summary-button">
            <form id="purchase-form" action="<?php echo e(route('item.processPurchase', ['item_id' => $item->id])); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="payment_method" value="" id="selected-payment-method">
                <button type="submit" class="purchase-summary-btn">購入する</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const paymentSelectBox = document.querySelector(".custom-payment-select");
    const selectedPaymentOption = document.getElementById("selectedPayment");
    const paymentDropdownOptions = document.getElementById("paymentDropdown");
    const paymentOptions = document.querySelectorAll(".dropdown-option");
    const paymentInput = document.getElementById("paymentInput");

    if (paymentSelectBox) {
        paymentSelectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            paymentDropdownOptions.style.display = paymentDropdownOptions.style.display === "block" ? "none" : "block";
        });

        paymentOptions.forEach(option => {
            option.addEventListener("click", function () {
                // すべてのオプションの "✓" を削除
                paymentOptions.forEach(opt => opt.classList.remove("selected"));

                // 選択されたオプションに "✓" を追加
                option.classList.add("selected");

                // **選択した項目を表示**
                selectedPaymentOption.textContent = option.textContent.trim();
                paymentInput.value = option.dataset.value;

                // **選択後にドロップダウンを確実に閉じる**
                setTimeout(() => {
                    paymentDropdownOptions.style.display = "none";
                }, 100);
            });
        });

        // **外部クリックでドロップダウンを閉じる**
        document.addEventListener("click", function (event) {
            if (!paymentSelectBox.contains(event.target) && !paymentDropdownOptions.contains(event.target)) {
                paymentDropdownOptions.style.display = "none";
            }
        });

        // **キーボードの "Escape" でもドロップダウンを閉じる**
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                paymentDropdownOptions.style.display = "none";
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/purchase.blade.php ENDPATH**/ ?>