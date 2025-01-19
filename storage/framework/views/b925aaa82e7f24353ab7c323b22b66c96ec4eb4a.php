<?php $__env->startSection('title', 'メールアドレス認証'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/verify-email.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="verify-email-container">
    <h1>メールアドレス認証</h1>
    <p>ご登録頂きましたメールアドレスに認証リンクを送信致しました<br>
    以下の認証リンクをクリックして、メールアドレスの認証を完了してください。</p>

    
    <?php if(session('message')): ?>
        <p style="color: green;"><?php echo e(session('message')); ?></p>
    <?php endif; ?>

    <p>認証メールを受け取っていない場合は、以下のボタンを押してください</p>

    
    <form action="<?php echo e(route('verification.resend')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit" class="resend-button">認証メールを再送信</button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/auth/verify-email.blade.php ENDPATH**/ ?>