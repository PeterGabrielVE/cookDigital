<?php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::getValByName('company_logo');
?>

<div class="sidenav custom-sidenav" id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center">
        <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">
            <img src="<?php echo e($logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')); ?>" alt="<?php echo e(config('app.name', 'LeadGo')); ?>" class="navbar-brand-img">
        </a>
        <div class="ml-auto">
            <div class="sidenav-toggler sidenav-toggler-dark d-md-none" data-action="sidenav-unpin" data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="scrollbar-inner">
        <div class="div-mega">
            <ul class="navbar-nav navbar-nav-docs">
                <li class="nav-item">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e((Request::route()->getName() == 'dashboard') ? 'active' : ''); ?>">
                        <i class="fas fa-fire"></i><?php echo e(__('Dashboard')); ?>

                    </a>
                </li>
                <?php if(\Auth::user()->type == 'super admin'): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage user')): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->is('users*') ? 'active' : ''); ?>">
                                <i class="fas fa-users"></i><?php echo e(__('User')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(Gate::check('manage client') || Gate::check('manage user') || Gate::check('manage role')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e((Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' active':'collapsed'); ?>" href="#navbar-getting-staff" data-toggle="collapse" role="button" aria-expanded="<?php echo e((Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' true':'false'); ?>" aria-controls="navbar-getting-staff">
                                <i class="fas fa-users"></i><?php echo e(__('Staff')); ?>

                                <i class="fas fa-sort-up"></i>
                            </a>
                            <div class="collapse <?php echo e((Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' show':''); ?>" id="navbar-getting-staff">
                                <ul class="nav flex-column submenu-ul">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage user')): ?>
                                        <li class="nav-item <?php echo e((Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit') ? ' active' : ''); ?>">
                                            <a class="nav-link" href="<?php echo e(route('users.index')); ?>"><?php echo e(__('User')); ?></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage client')): ?>
                                        <li class="nav-item <?php echo e((Request::route()->getName() == 'clients.index' || Request::route()->getName() == 'clients.create' || Request::route()->getName() == 'clients.edit') ? ' active' : ''); ?>">
                                            <a class="nav-link" href="<?php echo e(route('clients.index')); ?>"><?php echo e(__('Client')); ?></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage role')): ?>
                                        <li class="nav-item <?php echo e((Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit') ? ' active' : ''); ?>">
                                            <a class="nav-link" href="<?php echo e(route('roles.index')); ?>"><?php echo e(__('Role')); ?></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(Gate::check('manage lead') || \Auth::user()->type=='client'): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('leads.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'leads')?'active':''); ?>">
                            <i class="fas fa-cube"></i><?php echo e(__('Leads')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage estimations')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('estimations.index')); ?>" class="nav-link <?php echo e((Request::segment(1) == 'estimations')?'active':''); ?>">
                            <i class="fas fa-paper-plane"></i><?php echo e(__('Estimation')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->type != 'super admin' && Auth::user()->type != 'client' && env('CHAT_MODULE') == 'yes'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::route()->getName() == 'chats') ? 'active' : ''); ?>" href="<?php echo e(url('chats')); ?>">
                            <i class="fas fa-comments"></i> <?php echo e(__('Chats')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage project')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'projects')?'active open':''); ?>" href="<?php echo e(route('projects.index')); ?>">
                            <i class="fas fa-tasks"></i> <?php echo e(__('Project')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage timesheet')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'timesheet')?'active open':''); ?>" href="<?php echo e(route('task.timesheetRecord')); ?>">
                            <i class="fas fa-clock"></i> <?php echo e(__('Timesheet')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(\Auth::user()->type!='super admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'calender')?'active open':''); ?>" href="<?php echo e(route('calender.index')); ?>">
                            <i class="fas fa-calendar"></i> <?php echo e(__('Calendar')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage order')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'orders')?'active':''); ?>" href="<?php echo e(route('order.index')); ?>">
                            <i class="fas fa-cart-plus"></i><?php echo e(__('Order')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage note')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'notes')?'active':''); ?>" href="<?php echo e(route('notes.index')); ?>">
                            <i class="fas fa-sticky-note"></i><?php echo e(__('Notes')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if((Gate::check('manage product') || Gate::check('manage invoice') || Gate::check('manage expense') || Gate::check('manage payment') || Gate::check('manage tax')) || \Auth::user()->type=='client'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) == 'invoices-payments' || Request::segment(1) == 'taxes')?' active':'collapsed'); ?>" href="#navbar-getting-sales" data-toggle="collapse" role="button" aria-expanded="<?php echo e((Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) ==
                        'invoices-payments' || Request::segment(1) == 'taxes')? 'true':'false'); ?>"
                           aria-controls="navbar-getting-sales">
                            <i class="fas fa-shopping-cart"></i><?php echo e(__('Sales')); ?>

                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div class="collapse <?php echo e((Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) == 'invoices-payments' || Request::segment(1) == 'taxes')?' show':''); ?>" id="navbar-getting-sales">
                            <ul class="nav flex-column submenu-ul">
                                <?php if(Gate::check('manage invoice') || \Auth::user()->type=='client'): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'invoices')?'active':''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('invoices.index')); ?>">
                                            <?php echo e(__('Invoice')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(Gate::check('manage payment') || \Auth::user()->type=='client'): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'invoices-payments')?'active':''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('invoices.payments')); ?>">
                                            <?php echo e(__('Payment')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(Gate::check('manage expense') || \Auth::user()->type=='client'): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'expenses')?'active open':''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('expenses.index')); ?>">
                                            <?php echo e(__('Expense')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage tax')): ?>
                                    <li class="nav-item <?php echo e((Request::segment(1) == 'taxes')?'active':''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('taxes.index')); ?>">
                                            <?php echo e(__('Tax Rates')); ?>

                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage lead stage') || Gate::check('manage project stage') || Gate::check('manage lead source') || Gate::check('manage label') || Gate::check('manage expense category') || Gate::check('manage payment')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e((Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')?' active':'collapsed'); ?>" href="#navbar-getting-constant" data-toggle="collapse" role="button"
                           aria-expanded="<?php echo e((Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')? 'true':'false'); ?>"
                           aria-controls="navbar-getting-constant">
                            <i class="fas fa-shopping-cart"></i><?php echo e(__('Constant')); ?>

                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div class="collapse <?php echo e((Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')?' show':''); ?>" id="navbar-getting-constant">
                            <ul class="nav flex-column submenu-ul">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage lead stage')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'leadstages.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('leadstages.index')); ?>"> <?php echo e(__('Lead Stage')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage project stage')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'projectstages.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('projectstages.index')); ?>"> <?php echo e(__('Project Stage')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage lead source')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'leadsources.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('leadsources.index')); ?>"><?php echo e(__('Lead Source')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage label')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'labels.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('labels.index')); ?>"> <?php echo e(__('Label')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage product unit')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'productunits.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('productunits.index')); ?>"><?php echo e(__('Product Unit')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage expense category')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'expensescategory.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('expensescategory.index')); ?>"><?php echo e(__('Expense Category')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage payment')): ?>
                                    <li class="nav-item <?php echo e((Request::route()->getName() == 'payments.index' ) ? 'active' : ''); ?>">
                                        <a class="nav-link" href="<?php echo e(route('payments.index')); ?>"><?php echo e(__('Payment Method')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item <?php echo e((Request::segment(1) == 'bugstatus')?'active open':''); ?>" href="<?php echo e(route('bugstatus.index')); ?>">
                                    <a href="<?php echo e(route('bugstatus.index')); ?>" class="nav-link"><?php echo e(__('Bug Status')); ?></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage email templates')): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->is('email_template*') ? 'active' : ''); ?>" href="<?php echo e(route('email_template.index')); ?>">
                            <i class="fas fa-envelope"></i><?php echo e(__('Email Templates')); ?>

                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Gate::check('manage company settings')): ?>
                    <li class="nav-item  <?php echo e((Request::route()->getName() == 'company.setting') ? ' active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('company.setting')); ?>">
                            <i class="fas fa-sliders-h"></i>
                            <span class="nav-link-text"><?php echo e(__('Company Setting')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH C:\wamp\www\workGo\resources\views/partials/admin/menu.blade.php ENDPATH**/ ?>