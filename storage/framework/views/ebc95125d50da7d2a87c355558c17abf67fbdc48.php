<?php $__env->startSection('title', 'プロフィール設定'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/edit.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function previewImage(event) {
        const input = event.target;
        console.log('File selected:', input.files);
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
        }
    }

    // ファイル選択時のログ出力
    document.getElementById('profile_image').addEventListener('change', (event) => {
    const file = event.target.files[0];
    console.log('選択されたファイル:', file);
    if (file) {
        console.log('ファイル名:', file.name);
        console.log('ファイルタイプ:', file.type);
        console.log('ファイルサイズ:', file.size);
    } else {
        console.log('ファイルが選択されていません');
    }
});
</script>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>

<form action="<?php echo e(route('mypage.profile.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="profile-image-section">
        <div class="image-preview">
        <img 
        id="preview" 
        src="<?php echo e(session('profile_image_temp') ?: (auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : '')); ?>" 
        alt="プロフィール画像" 
        class="<?php echo e(session('profile_image_temp') || auth()->user()->profile_image ? 'show' : ''); ?>">
        <?php if(!auth()->user()->profile_image && !session('profile_image_temp')): ?>
            <div id="placeholder" class="placeholder"></div>
        <?php endif; ?>
        </div>

        <label class="btn-select-image">
            画像を選択する
            <input type="file" name="profile_image" id="profile_image" onchange="previewImage(event)" style="display: none;">
        </label>
        <?php if($errors->has('profile_image')): ?>
            <div class="error-message">
                <?php echo e($errors->first('profile_image')); ?>

            </div>
        <?php endif; ?>
    </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" id="name" value="<?php echo e(old('name', auth()->user()->name)); ?>">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error-message"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="<?php echo e(old('postal_code', auth()->user()->postal_code)); ?>">
            <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error-message"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="<?php echo e(old('address', auth()->user()->address)); ?>">
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="error-message"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="<?php echo e(old('building', auth()->user()->building)); ?>">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/profile/edit.blade.php ENDPATH**/ ?>