<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'coachtech'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/sanitize.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="container">
        <header class="main-header">
            <div class="logo">
                <img src="<?php echo e(asset('storage/items/logo.svg')); ?>" alt="Logo">
            </div>
            <div class="header__search">
                <form action="<?php echo e(route('products.index')); ?>" method="GET">
                    <input type="text" name="search" placeholder="なにをお探しですか？" class="search-box" value="<?php echo e(request('search')); ?>">
                </form>
            </div>
            <div class="header__menu">
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>">ログイン</a>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="logout-btn">ログアウト</button>
                    </form>
                <?php endif; ?>

                <a href="<?php echo e(route('mypage')); ?>">マイページ</a>
                <a href="<?php echo e(route('item.create')); ?>" class="header__sell-btn">出品</a>
            </div>
        </header>

        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/main.blade.php ENDPATH**/ ?>