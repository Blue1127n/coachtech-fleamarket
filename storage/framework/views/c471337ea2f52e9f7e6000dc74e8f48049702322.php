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
                <div class="custom-condition-select">
                    <div class="selected-option" id="selectedCondition">選択してください</div>
                        <div class="dropdown-options" id="dropdownOptions">
                        <?php $__currentLoopData = $conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $condition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="dropdown-option" data-value="<?php echo e($condition->id); ?>">
                            <span class="check-icon"></span><?php echo e($condition->condition); ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <input type="hidden" name="condition_id" id="conditionInput">
                </div>
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
    // **カテゴリー選択の処理**
    const categoryOptions = document.querySelectorAll(".category-option");

    categoryOptions.forEach(option => {
        option.addEventListener("click", function () {
            const checkbox = this.querySelector(".category-checkbox");
            checkbox.checked = !checkbox.checked;
            this.classList.toggle("selected", checkbox.checked);
        });
    });

    // **商品の状態プルダウンの「✓」処理**
    const selectCondition = document.getElementById("condition_select");

    if (selectCondition) {
        Array.from(selectCondition.options).forEach(option => {
            option.dataset.originalText = option.textContent;
        });

        // **マウスホバー時に ✓ を追加**
        selectCondition.addEventListener("mouseover", function (event) {
            if (event.target.tagName === "OPTION") {
                event.target.textContent = `✓ ${event.target.dataset.originalText}`;
            }
        });

        // **マウスが離れたら元のテキストに戻す**
        selectCondition.addEventListener("mouseout", function (event) {
            if (event.target.tagName === "OPTION") {
                event.target.textContent = event.target.dataset.originalText;
            }
        });

        // **選択時にドロップダウンを閉じる**
        selectCondition.addEventListener("change", function () {
            setTimeout(() => {
                selectCondition.blur(); // フォーカスを外して閉じる
            }, 100);
        });
    }

    // **カスタムドロップダウンの処理**
    const selectBox = document.querySelector(".custom-condition-select");
    const selectedOption = document.getElementById("selectedCondition");
    const dropdownOptions = document.getElementById("dropdownOptions");
    const options = document.querySelectorAll(".dropdown-option");
    const conditionInput = document.getElementById("conditionInput");

    if (selectBox) {
        selectBox.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownOptions.style.display = dropdownOptions.style.display === "block" ? "none" : "block";
        });

        options.forEach(option => {
            option.addEventListener("click", function () {
                options.forEach(opt => opt.classList.remove("selected"));
                option.classList.add("selected");

                // **選択された項目を表示**
                selectedOption.textContent = option.textContent.trim();
                conditionInput.value = option.dataset.value;

                // **ドロップダウンを閉じる**
                dropdownOptions.style.display = "none";
            });
        });

        // **外部クリックでドロップダウンを閉じる**
        document.addEventListener("click", function (event) {
            if (!selectBox.contains(event.target) && !dropdownOptions.contains(event.target)) {
                dropdownOptions.style.display = "none";
            }
        });

        // **選択時に自動で閉じる**
        dropdownOptions.addEventListener("click", function () {
            setTimeout(() => {
                dropdownOptions.style.display = "none";
            }, 100);
        });
    }
});


</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/item/sell.blade.php ENDPATH**/ ?>