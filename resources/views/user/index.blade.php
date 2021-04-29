@extends('layouts.admin')
@section('page-title')
    {{__('Manage User')}}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('create user')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="{{__('Create User')}}" data-url="{{route('users.create')}}">
                    <i class="fas fa-plus"></i> {{__('Add New')}}
                </a>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        @foreach($users as $user)
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card profile-card">
                    @if(Gate::check('edit user') || Gate::check('delete user'))
                        <div class="edit-profile user-text">
                            @if($user->is_active == 1)
                                @if((Gate::check('edit user') || Gate::check('delete user')))
                                    <div class="dropdown action-item">
                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @can('edit user')
                                                <a href="#" class="dropdown-item" data-url="{{ route('users.edit',$user->id) }}" data-ajax-popup="true" data-title="{{__('Edit User')}}">{{__('Edit')}}</a>
                                            @endcan
                                            @can('delete user')
                                                <a class="dropdown-item" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$user['id']}}').submit();">
                                                    @if($user->delete_status == 1){{__('Delete')}} @else {{__('Restore')}}@endif
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user['id']],'id'=>'delete-form-'.$user['id']]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>
                                @endif
                            @else
                                <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                            @endif
                        </div>
                    @endif
                    <div class="avatar-parent-child">
                        <img src="{{(!empty($user->avatar))? asset(Storage::url("avatar/".$user->avatar)): asset(Storage::url("avatar/avatar.png"))}}" class="avatar rounded-circle avatar-xl">
                    </div>
                    <h4 class="h4 mb-0 mt-2">{{$user->name}}</h4>
                    <h5 class="office-time mb-0">{{$user->email}}</h5>
                    @if($user->delete_status == 0)
                        <h5 class="office-time mb-0">{{__('Deleted')}}</h5>
                    @endif
                    <div class="sal-right-card">
                        <span class="badge badge-pill badge-blue">{{ucfirst($user->type)}}</span>
                    </div>
                    <div class="row text-center">
                        @if(\Auth::user()->type=='super admin')
                            <div class="col-6 text-center">
                                <span class="d-block font-weight-bold mb-0 mt-2 text-sm">{{!empty($user->getPlan)?$user->getPlan->name : ''}}</span>
                            </div>
                            <div class="col-6 text-center Id">
                                <a href="#" class="btn-sm" data-url="{{ route('plan.upgrade',$user->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}">{{__('Upgrade Plan')}}</a>
                            </div>
                            <div class="col-12 text-center pt-3">
                                <span class="text-dark text-xs">{{__('Plan Expired : ') }} {{!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date): __('Unlimited')}}</span>
                            </div>
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-users"></i>{{$user->total_company_user($user->id)}}</div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i>{{$user->total_company_project($user->id)}}</div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number border-none"><i class="fas fa-tasks"></i>{{$user->total_company_client($user->id)}}</div>
                            </div>
                        @else
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-briefcase"></i>{{$user->user_project()}}</div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i>{{\Auth::user()->priceFormat($user ->user_expense())}}</div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number border-none"><i class="fas fa-tasks"></i>{{$user->user_assign_task()}}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
