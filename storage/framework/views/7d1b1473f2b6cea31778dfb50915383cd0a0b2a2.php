<nav class="navbar navbar-main navbar-expand-lg navbar-border n-top-header" id="navbar-main">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-user d-lg-none ml-auto">
            <ul class="navbar-nav flex-row align-items-center">
                <li class="nav-item">
                    <a href="#" class="nav-link nav-link-icon sidenav-toggler" data-action="sidenav-pin" data-target="#sidenav-main"><i class="fas fa-bars"></i></a>
                </li>
                <?php if(\Auth::user()->type != 'super admin'): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link nav-link-icon text-dark" data-action="omnisearch-open" data-target="#omnisearch"><i class="fas fa-search"></i></a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type != 'super admin' && \Auth::user()->type != 'client'): ?>
                    <li class="nav-item dropdown dropdown-animate">
                        <a class="nav-link nav-link-icon message-toggle-msg" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope m-0 text-dark"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg dropdown-menu-arrow p-0">
                            <div class="py-3 px-3">
                                <h5 class="heading h6 mb-0 float-left"><?php echo e(__('Messages')); ?></h5>
                                <a href="#" class="link link-sm mark_all_as_read_message float-right"><?php echo e(__('Marl All As Read')); ?></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="list-group list-group-flush max-380 dropdown-list-message-msg">
                            </div>
                            <div class="py-3 text-center">
                                <a href="<?php echo e(route('chats')); ?>" class="link link-sm link--style-3"><?php echo e(__('View all')); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type != 'super admin'): ?>
                    <li class="nav-item dropdown dropdown-animate">
                        <?php
                            $notifications = \Auth::user()->notifications();
                        ?>
                        <a class="nav-link nav-link-icon notification-toggle <?php if(count($notifications)): ?>beep <?php endif; ?>" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell m-0 text-dark"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg dropdown-menu-arrow p-0 notification-dropdown">
                            <div class="py-3 px-3">
                                <h5 class="heading h6 mb-0 float-left"><?php echo e(__('Notifications')); ?></h5>
                                <a href="#" class="link link-sm mark_all_as_read float-right"><?php echo e(__('Marl All As Read')); ?></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="list-group list-group-flush max-380" id="notification-list-mini">
                                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $notification->toHtml(); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="nav-item dropdown dropdown-animate">
                    <a class="nav-link pr-lg-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <span class="avatar avatar-sm rounded-circle">
                            <img alt="#" src="<?php echo e((!empty(\Auth::user()->avatar)? asset(Storage::url('avatar/'.\Auth::user()->avatar)) : asset(Storage::url('avatar/avatar.png')))); ?>" class="icon-user avatar rounded-circle">
                          </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right dropdown-menu-arrow">
                        <h6 class="dropdown-header px-0"><?php echo e(__('Hi,')); ?> <?php echo e(Auth::user()->name); ?>!</h6>
                        <a href="<?php echo e(route('profile')); ?>" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo e(__('My Profile')); ?></span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span><?php echo e(__('Logout')); ?></span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse navbar-collapse-fade" id="navbar-main-collapse">
            <ul class="navbar-nav align-items-center d-none d-lg-flex">
                <li class="nav-item">
                    <a href="#" class="nav-link nav-link-icon sidenav-toggler" data-action="sidenav-pin" data-target="#sidenav-main"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item dropdown dropdown-animate">
                    <a class="nav-link pr-lg-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media media-pill align-items-center">
                            <span class="avatar rounded-circle">
                                <img alt="#" src="<?php echo e((!empty(\Auth::user()->avatar)? asset(Storage::url('avatar/'.\Auth::user()->avatar)) : asset(Storage::url('avatar/avatar.png')))); ?>" class="icon-user avatar rounded-circle">
                            </span>
                            <div class="ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold"><?php echo e(Auth::user()->name); ?></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right dropdown-menu-arrow">
                        <h6 class="dropdown-header px-0"><?php echo e(__('Hi,')); ?> <?php echo e(Auth::user()->name); ?>!</h6>
                        <a href="<?php echo e(route('profile')); ?>" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo e(__('My Profile')); ?></span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> <?php echo e(__('Logout')); ?>

                        </a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </li>
            </ul>

            <ul class="navbar-nav ml-lg-auto align-items-lg-center">
                <?php if(\Auth::user()->type != 'super admin'): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link nav-link-icon text-dark" data-action="omnisearch-open" data-target="#omnisearch"><i class="fas fa-search"></i></a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->type != 'super admin' && Auth::user()->type != 'client'): ?>
                    <li class="nav-item dropdown dropdown-animate">
                        <a class="nav-link nav-link-icon message-toggle-msg" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-envelope m-0 text-dark"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg dropdown-menu-arrow p-0">
                            <div class="py-3 px-3">
                                <h5 class="heading h6 mb-0 float-left"><?php echo e(__('Messages')); ?></h5>
                                <a href="#" class="link link-sm mark_all_as_read_message float-right"><?php echo e(__('Marl All As Read')); ?></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="list-group list-group-flush max-380 dropdown-list-message-msg">
                            </div>
                            <div class="py-3 text-center">
                                <a href="<?php echo e(route('chats')); ?>" class="link link-sm link--style-3"><?php echo e(__('View all')); ?></a>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type != 'super admin'): ?>
                    <li class="nav-item dropdown dropdown-animate">
                        <?php
                            $notifications = \Auth::user()->notifications();
                        ?>
                        <a class="nav-link nav-link-icon notification-toggle <?php if(count($notifications)): ?>beep <?php endif; ?>" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell m-0 text-dark"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg dropdown-menu-arrow p-0 notification-dropdown">
                            <div class="py-3 px-3">
                                <h5 class="heading h6 mb-0 float-left"><?php echo e(__('Notifications')); ?></h5>
                                <a href="#" class="link link-sm mark_all_as_read float-right"><?php echo e(__('Marl All As Read')); ?></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="list-group list-group-flush max-380" id="notification-list">
                                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $notification->toHtml(); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <div class="dropdown global-icon">
                        <button class="dropdown-toggle" type="button" id="dropdownLanguage" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-globe-europe"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLanguage">
                            <?php $__currentLoopData = Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('change.language',$lang)); ?>" class="dropdown-item <?php echo e((basename(App::getLocale()) == $lang) ? 'text-danger' : ''); ?>"><?php echo e(Str::upper($lang)); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create language')): ?>
                                <div class="dropdown-divider m-0"></div>
                                <a href="<?php echo e(route('manage.language',[basename(App::getLocale())])); ?>" class="dropdown-item text-primary"><?php echo e(__('Manage Language')); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\wamp\www\workGo\resources\views/partials/admin/header.blade.php ENDPATH**/ ?>