<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title> <?php echo $__env->yieldContent('title'); ?> &dash; <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'Workgo')); ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset(Storage::url('logo/favicon.png'))); ?>" type="image">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ac.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/stylesheet.css')); ?>">
    <?php echo $__env->yieldPushContent('head'); ?>
</head>

<body>
<div class="login-contain">
    <div class="login-inner-contain">
        <a class="navbar-brand" href="#">
            <img src="<?php echo e(asset(Storage::url('logo/logo.png'))); ?>" alt="<?php echo e(config('app.name', 'WorkGo')); ?>" class="navbar-brand-img">
        </a>
        <?php echo $__env->yieldContent('content'); ?>
        <h5 class="copyright-text">
            <?php echo e(__('Copyright')); ?> &copy; <?php echo e((Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'WorkGo')); ?> <?php echo e(date('Y')); ?>

        </h5>
        <?php echo $__env->yieldContent('language-bar'); ?>
    </div>
</div>
<script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
<?php echo $__env->yieldPushContent('script'); ?>
</body>
</html>
<?php /**PATH C:\wamp\www\workGo\resources\views/layouts/auth.blade.php ENDPATH**/ ?>