@php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::getValByName('company_logo');
@endphp

<div class="sidenav custom-sidenav" id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center">
        <a class="navbar-brand" href="{{route('dashboard')}}">
            <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}" alt="{{ config('app.name', 'LeadGo') }}" class="navbar-brand-img">
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
                    <a href="{{route('dashboard')}}" class="nav-link {{ (Request::route()->getName() == 'dashboard') ? 'active' : '' }}">
                        <i class="fas fa-fire"></i>{{__('Dashboard')}}
                    </a>
                </li>
                @if(\Auth::user()->type == 'super admin')
                    @can('manage user')
                        <li class="nav-item">
                            <a href="{{route('users.index')}}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>{{__('User')}}
                            </a>
                        </li>
                    @endcan
                @else
                    @if(Gate::check('manage client') || Gate::check('manage user') || Gate::check('manage role'))
                        <li class="nav-item">
                            <a class="nav-link {{ (Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' active':'collapsed'}}" href="#navbar-getting-staff" data-toggle="collapse" role="button" aria-expanded="{{ (Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' true':'false'}}" aria-controls="navbar-getting-staff">
                                <i class="fas fa-users"></i>{{__('Staff')}}
                                <i class="fas fa-sort-up"></i>
                            </a>
                            <div class="collapse {{ (Request::segment(1) == 'users' || Request::segment(1) == 'clients' || Request::segment(1) == 'roles')?' show':''}}" id="navbar-getting-staff">
                                <ul class="nav flex-column submenu-ul">
                                    @can('manage user')
                                        <li class="nav-item {{ (Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{route('users.index')}}">{{__('User')}}</a>
                                        </li>
                                    @endcan
                                    @can('manage client')
                                        <li class="nav-item {{ (Request::route()->getName() == 'clients.index' || Request::route()->getName() == 'clients.create' || Request::route()->getName() == 'clients.edit') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{route('clients.index')}}">{{__('Client')}}</a>
                                        </li>
                                    @endcan
                                    @can('manage role')
                                        <li class="nav-item {{ (Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit') ? ' active' : '' }}">
                                            <a class="nav-link" href="{{route('roles.index')}}">{{__('Role')}}</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif
                @endif
                @if(Gate::check('manage lead') || \Auth::user()->type=='client')
                    <li class="nav-item">
                        <a href="{{route('leads.index')}}" class="nav-link {{ (Request::segment(1) == 'leads')?'active':''}}">
                            <i class="fas fa-cube"></i>{{__('Leads')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage estimations'))
                    <li class="nav-item">
                        <a href="{{route('estimations.index')}}" class="nav-link {{ (Request::segment(1) == 'estimations')?'active':''}}">
                            <i class="fas fa-paper-plane"></i>{{__('Estimation')}}
                        </a>
                    </li>
                @endif
                @if(Auth::user()->type != 'super admin' && Auth::user()->type != 'client' && env('CHAT_MODULE') == 'yes')
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::route()->getName() == 'chats') ? 'active' : '' }}" href="{{url('chats')}}">
                            <i class="fas fa-comments"></i> {{__('Chats')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage project'))
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'projects')?'active open':''}}" href="{{ route('projects.index') }}">
                            <i class="fas fa-briefcase"></i> {{__('Project')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage project'))
                <li class="nav-item">
                    <a class="nav-link {{ (Request::segment(1) == 'tasks')?'active open':''}}" href="{{ route('getAllTasks') }}">
                        <i class="fas fa-tasks"></i> {{__('Tasks')}}
                    </a>
                </li>
                @endif
                @if(Gate::check('manage timesheet'))
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'timesheet')?'active open':''}}" href="{{ route('task.timesheetRecord') }}">
                            <i class="fas fa-clock"></i> {{__('Timesheet')}}
                        </a>
                    </li>
                @endif
                @if(\Auth::user()->type!='super admin')
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'calender')?'active open':''}}" href="{{ route('calender.index') }}">
                            <i class="fas fa-calendar"></i> {{__('Calendar')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage order'))
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'orders')?'active':''}}" href="{{ route('order.index') }}">
                            <i class="fas fa-cart-plus"></i>{{__('Order')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage note'))
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'notes')?'active':''}}" href="{{ route('notes.index') }}">
                            <i class="fas fa-sticky-note"></i>{{__('Notes')}}
                        </a>
                    </li>
                @endif
                @if((Gate::check('manage product') || Gate::check('manage invoice') || Gate::check('manage expense') || Gate::check('manage payment') || Gate::check('manage tax')) || \Auth::user()->type=='client')
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) == 'invoices-payments' || Request::segment(1) == 'taxes')?' active':'collapsed'}}" href="#navbar-getting-sales" data-toggle="collapse" role="button" aria-expanded="{{ (Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) ==
                        'invoices-payments' || Request::segment(1) == 'taxes')? 'true':'false'}}"
                           aria-controls="navbar-getting-sales">
                            <i class="fas fa-shopping-cart"></i>{{__('Sales')}}
                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div class="collapse {{ (Request::segment(1) == 'products' || Request::segment(1) == 'expenses' || Request::segment(1) == 'invoices' || Request::segment(1) == 'invoices-payments' || Request::segment(1) == 'taxes')?' show':''}}" id="navbar-getting-sales">
                            <ul class="nav flex-column submenu-ul">
                                @if(Gate::check('manage invoice') || \Auth::user()->type=='client')
                                    <li class="nav-item {{ (Request::segment(1) == 'invoices')?'active':''}}">
                                        <a class="nav-link" href="{{ route('invoices.index') }}">
                                            {{__('Invoice')}}
                                        </a>
                                    </li>
                                @endcan
                                @if(Gate::check('manage payment') || \Auth::user()->type=='client')
                                    <li class="nav-item {{ (Request::segment(1) == 'invoices-payments')?'active':''}}">
                                        <a class="nav-link" href="{{ route('invoices.payments') }}">
                                            {{__('Payment')}}
                                        </a>
                                    </li>
                                @endif
                                @if(Gate::check('manage expense') || \Auth::user()->type=='client')
                                    <li class="nav-item {{ (Request::segment(1) == 'expenses')?'active open':''}}">
                                        <a class="nav-link" href="{{ route('expenses.index') }}">
                                            {{__('Expense')}}
                                        </a>
                                    </li>
                                @endif
                                @can('manage tax')
                                    <li class="nav-item {{ (Request::segment(1) == 'taxes')?'active':''}}">
                                        <a class="nav-link" href="{{ route('taxes.index') }}">
                                            {{__('Tax Rates')}}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif
                @if(Gate::check('manage lead stage') || Gate::check('manage project stage') || Gate::check('manage lead source') || Gate::check('manage label') || Gate::check('manage expense category') || Gate::check('manage payment'))
                    <li class="nav-item">
                        <a class="nav-link {{ (Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')?' active':'collapsed'}}" href="#navbar-getting-constant" data-toggle="collapse" role="button"
                           aria-expanded="{{ (Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')? 'true':'false'}}"
                           aria-controls="navbar-getting-constant">
                            <i class="fas fa-shopping-cart"></i>{{__('Constant')}}
                            <i class="fas fa-sort-up"></i>
                        </a>
                        <div class="collapse {{ (Request::segment(1) == 'leadstages' || Request::segment(1) == 'projectstages' ||  Request::segment(1) == 'leadsources' ||  Request::segment(1) == 'labels' ||  Request::segment(1) == 'productunits' ||  Request::segment(1) == 'expensescategory' ||  Request::segment(1) == 'payments' ||  Request::segment(1) == 'bugstatus')?' show':''}}" id="navbar-getting-constant">
                            <ul class="nav flex-column submenu-ul">
                                @can('manage lead stage')
                                    <li class="nav-item {{ (Request::route()->getName() == 'leadstages.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('leadstages.index')}}"> {{__('Lead Stage')}}</a>
                                    </li>
                                @endcan
                                @can('manage project stage')
                                    <li class="nav-item {{ (Request::route()->getName() == 'projectstages.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('projectstages.index')}}"> {{__('Project Stage')}}</a>
                                    </li>
                                @endcan
                                @can('manage lead source')
                                    <li class="nav-item {{ (Request::route()->getName() == 'leadsources.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('leadsources.index')}}">{{__('Lead Source')}}</a>
                                    </li>
                                @endcan
                                @can('manage label')
                                    <li class="nav-item {{ (Request::route()->getName() == 'labels.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('labels.index')}}"> {{__('Label')}}</a>
                                    </li>
                                @endcan
                                @can('manage product unit')
                                    <li class="nav-item {{ (Request::route()->getName() == 'productunits.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('productunits.index')}}">{{__('Product Unit')}}</a>
                                    </li>
                                @endcan
                                @can('manage expense category')
                                    <li class="nav-item {{ (Request::route()->getName() == 'expensescategory.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('expensescategory.index')}}">{{__('Expense Category')}}</a>
                                    </li>
                                @endcan
                                @can('manage payment')
                                    <li class="nav-item {{ (Request::route()->getName() == 'payments.index' ) ? 'active' : '' }}">
                                        <a class="nav-link" href="{{route('payments.index')}}">{{__('Payment Method')}}</a>
                                    </li>
                                @endcan
                                <li class="nav-item {{ (Request::segment(1) == 'bugstatus')?'active open':''}}" href="{{ route('bugstatus.index') }}">
                                    <a href="{{ route('bugstatus.index') }}" class="nav-link">{{__('Bug Status')}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                @if(Gate::check('manage email templates'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('email_template*') ? 'active' : '' }}" href="{{route('email_template.index')}}">
                            <i class="fas fa-envelope"></i>{{__('Email Templates')}}
                        </a>
                    </li>
                @endif
                @if(Gate::check('manage company settings'))
                    <li class="nav-item  {{ (Request::route()->getName() == 'company.setting') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('company.setting') }}">
                            <i class="fas fa-sliders-h"></i>
                            <span class="nav-link-text">{{  __('Company Setting') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
