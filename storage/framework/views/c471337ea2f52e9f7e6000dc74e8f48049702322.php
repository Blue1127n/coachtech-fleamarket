<?php $__env->startSection('title', '商品の出品'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/sell.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="sell-container">
    <h2 class="title">商品を出品</h2>

    <form action="<?php echo e(route('item.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <div class="form-group image-upload">
                <label class="image-label">商品画像</label>
                    <div class="image-upload-box">
                        <input type="file" name="image" id="imageInput" accept="image/*" class="image-input">
                        <label for="imageInput" class="image-button">画像を選択する</label>
                    </div>
            </div>

            <div class="form-group product-details">
                <h2>商品の詳細</h2>
            </div>

            <div class="form-group category-group">
                <label class="category-label">カテゴリー</label>
                <div class="category-options">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="category-option">
                            <input type="checkbox" name="category[]" value="<?php echo e($category->id); ?>" class="category-checkbox">
                            <span class="category-name"><?php echo e($category->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-group condition-group">
                <label class="condition-label">商品の状態</label>
                <select name="condition_id" class="condition-select" style="background-image: url('<?php echo e(asset('storage/items/triangle.svg')); ?>');">
                    <option value="" disabled selected hidden>選択してください</option>
                    <?php $__currentLoopData = $conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $condition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($condition->id); ?>"><?php echo e($condition->condition); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group product-name-description">
                <h2>商品名と説明</h2>
            </div>

            <div class="form-group product-name">
                <label class="name-label">商品名</label>
                <input type="text" name="name" class="name-input" required>
            </div>

            <div class="form-group product-description">
                <label class="description-label">商品の説明</label>
                <textarea name="description" class="description-textarea" rows="4" required></textarea>
            </div>

            <div class="form-group price">
                <label class="price-label">販売価格</label>
                <input type="number" name="price" class="price-input" required placeholder="¥">
            </div>

            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const categoryOptions = document.querySelectorAll(".category-option");

    categoryOptions.forEach(option => {
        option.addEventListener("click", function () {
            const checkbox = this.querySelector(".category-checkbox");
            checkbox.checked = !checkbox.checked; // チェックのON/OFFを切り替え

            // クラスを切り替えて選択状態の見た目を変更
            this.classList.toggle("selected", checkbox.checked);
        });
    });
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/sell.blade.php ENDPATH**/ ?>