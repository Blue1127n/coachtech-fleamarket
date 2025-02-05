<?php $__env->startSection('title', '送付先住所の変更'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/address_change.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="address-change-container">
    <h1 class="page-title">住所の変更</h1>

    <form action="<?php echo e(route('item.updateAddress', ['item_id' => $item->id])); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('POST'); ?>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="<?php echo e(old('postal_code', $shippingPostalCode)); ?>">
            <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="error-message"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="<?php echo e(old('address', $shippingAddress)); ?>">
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="error-message"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="<?php echo e(old('building', !is_null($shippingBuilding) ? $shippingBuilding : auth()->user()->building)); ?>">
            <?php $__errorArgs = ['building'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="error-message"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <button type="submit" class="update-btn">更新する</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/address_change.blade.php ENDPATH**/ ?>