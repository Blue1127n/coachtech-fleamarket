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
                <select name="payment_method" id="payment_method" class="payment-select" style="background-image: url('<?php echo e(asset('storage/items/triangle.svg')); ?>');">
                    <option value="" class="default-option" disabled hidden selected>選択してください</option>
                    <option value="コンビニ払い" class="convenience-option">コンビニ払い</option>
                    <option value="カード支払い" class="card-option">カード支払い</option>
                </select>
            </form>
        </div>

        <div class="shipping-address">
            <h2>配送先</h2>
            <div class="shipping-content">
                <?php
                    // 取引情報を取得（なければ users テーブルのデータを使う）
                    $transaction = \App\Models\Transaction::where('item_id', $item->id)
                                                            ->where('buyer_id', auth()->id())
                                                            ->first();

                    $postalCode = $transaction->shipping_postal_code ?? auth()->user()->postal_code ?? '未登録';
                    $address = $transaction->shipping_address ?? auth()->user()->address ?? '未登録';
                    $building = $transaction ? $transaction->shipping_building : (auth()->user()->building ?? '未登録');
                ?>

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
            <form id="purchase-form" action="<?php echo e(route('item.confirmPurchase', ['item_id' => $item->id])); ?>" method="POST">
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
    const select = document.getElementById("payment_method");
    const selectedMethod = document.getElementById("selected-method");
    const selectedPaymentMethod = document.getElementById("selected-payment-method");
    const purchaseForm = document.getElementById("purchase-form");

    // **初回表示時は必ず「選択してください」にリセット**
    localStorage.removeItem("selectedPaymentMethod");  // 過去の選択を削除
    select.selectedIndex = 0;  // 一番上（デフォルト）の選択肢を表示
    selectedMethod.textContent = "未選択";
    selectedPaymentMethod.value = "";

    select.addEventListener("change", function () {
        if (select.value) {
            selectedMethod.textContent = select.value;
            selectedPaymentMethod.value = select.value;

            // **選択された場合のみ localStorage に保存**
            localStorage.setItem("selectedPaymentMethod", select.value);
        } else {
            // **「選択してください」を選んだら localStorage から削除**
            localStorage.removeItem("selectedPaymentMethod");
            selectedMethod.textContent = "未選択";
            selectedPaymentMethod.value = "";
        }

        // **選択肢の見た目を修正**
        for (let option of select.options) {
            option.textContent = option.value === select.value ? `✓ ${option.value}` : option.value;
        }

        // **✓を一時的に表示し、100ms後に元の表示に戻す**
        setTimeout(() => {
            for (let option of select.options) {
                option.textContent = option.value;
            }
        }, 100);
    });

    // **フォーム送信時のバリデーション**
    purchaseForm.addEventListener("submit", function (event) {
        if (!selectedPaymentMethod.value) {
            alert("支払い方法を選択してください");
            event.preventDefault();
        }
    });
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/purchase.blade.php ENDPATH**/ ?>