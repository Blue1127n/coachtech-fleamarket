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
            <form action="<?php echo e(route('item.processPurchase', ['item_id' => $item->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <select name="payment_method" id="payment_method" class="payment-select" style="background-image: url('<?php echo e(asset('storage/items/triangle.svg')); ?>');">
                    <option value="" class="default-option" disabled hidden>選択してください</option>
                    <option value="コンビニ払い" class="convenience-option">コンビニ払い</option>
                    <option value="カード支払い" class="card-option">カード支払い</option>
                </select>
                <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="error-message" style="color: red;"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

                    // 最初は users テーブルのデータを使用し、変更があった場合は transactions テーブルのデータを使用
                    $postalCode = $transaction && $transaction->shipping_postal_code ? $transaction->shipping_postal_code : auth()->user()->postal_code;
                    $address = $transaction && $transaction->shipping_address ? $transaction->shipping_address : auth()->user()->address;
                    $building = $transaction && $transaction->shipping_building ? $transaction->shipping_building : auth()->user()->building;
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
    const select = document.getElementById("payment_method");
    const selectedMethod = document.getElementById("selected-method");
    const selectedPaymentMethod = document.getElementById("selected-payment-method");
    const purchaseForm = document.getElementById("purchase-form");

    // **バリデーションエラー時に old() の値を復元**
    const oldPaymentMethod = "<?php echo e(old('payment_method', '')); ?>";

    if (select) {
        if (oldPaymentMethod) {
            select.value = oldPaymentMethod;
            selectedMethod.textContent = oldPaymentMethod;
            selectedPaymentMethod.value = oldPaymentMethod;
        } else {
            select.selectedIndex = 0;
            selectedMethod.textContent = "未選択";
            selectedPaymentMethod.value = "";
        }

        // **プルダウンの変更イベントで hidden input を更新**
        select.addEventListener("change", function () {
            selectedMethod.textContent = select.value;
            selectedPaymentMethod.value = select.value;
            localStorage.setItem("selectedPaymentMethod", select.value);

            // **選択されたオプションに "✓" を付与**
            select.querySelectorAll("option").forEach(option => {
                option.textContent = option.value === select.value ? `✓ ${option.value}` : option.value;
            });

            // **200ms 後に元の状態に戻す**
            setTimeout(() => {
                select.querySelectorAll("option").forEach(option => {
                    option.textContent = option.value;
                });
            }, 200);
        });

        // **購入ボタンのクリック時にバリデーションをチェック**
        purchaseForm.addEventListener("submit", function (event) {
            if (!select.value) {
                event.preventDefault();
                alert("支払い方法を選択してください。");
            }
        });
    }
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/purchase.blade.php ENDPATH**/ ?>