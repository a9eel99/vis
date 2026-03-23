<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__('login')); ?> - <?php echo e(__('app_name')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    
</head>
<body>

<div class="login-page">
    
    <div class="login-form-side">
        
        <div style="position:absolute;top:1.5rem;<?php echo e(app()->getLocale() === 'ar' ? 'left' : 'right'); ?>:1.5rem;display:flex;gap:.5rem">
            <a href="<?php echo e(route('lang.switch', 'ar')); ?>" class="lang-btn <?php echo e(app()->getLocale() === 'ar' ? 'active' : ''); ?>">عربي</a>
            <a href="<?php echo e(route('lang.switch', 'en')); ?>" class="lang-btn <?php echo e(app()->getLocale() === 'en' ? 'active' : ''); ?>">EN</a>
        </div>

        <div class="login-logo">VIS</div>
        <h1><?php echo e(__('welcome_back')); ?></h1>
        <p class="login-subtitle"><?php echo e(__('login_subtitle')); ?></p>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span><?php echo e($error); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" class="login-form">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label class="form-label"><?php echo e(__('email')); ?></label>
                <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" placeholder="example@vis.com" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label"><?php echo e(__('password')); ?></label>
                <div class="password-wrapper">
                    <input type="password" name="password" class="form-control" id="password" placeholder="••••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember"><?php echo e(__('remember_me')); ?></label>
            </div>

            <button type="submit" class="btn-login">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                <?php echo e(__('login')); ?>

            </button>
        </form>

        <div class="login-demo-info">
            <div style="margin-bottom:.25rem"><?php echo e(__('demo_credentials')); ?></div>
            <code>admin@vis.local</code> / <code>password</code>
        </div>
    </div>

    
    <div class="login-hero-side">
        <div class="hero-badge">🚀 <?php echo e(__('version')); ?></div>
        <h2><?php echo e(__('hero_title')); ?></h2>
        <p class="hero-desc"><?php echo e(__('hero_desc')); ?></p>

        <div class="hero-features">
            <div class="hero-feature">
                <div class="hero-feature-icon blue">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                <span><?php echo e(__('hero_feature_1')); ?></span>
            </div>
            <div class="hero-feature">
                <div class="hero-feature-icon green">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                </div>
                <span><?php echo e(__('hero_feature_2')); ?></span>
            </div>
            <div class="hero-feature">
                <div class="hero-feature-icon red">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <span><?php echo e(__('hero_feature_3')); ?></span>
            </div>
        </div>

        <div class="login-footer">© VIS <?php echo e(date('Y')); ?> - <?php echo e(__('all_rights')); ?></div>
    </div>
</div>

<script>
function togglePassword() {
    const p = document.getElementById('password');
    p.type = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\vis\resources\views/auth/login.blade.php ENDPATH**/ ?>