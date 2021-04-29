@extends('layouts.admin')
@section('page-title')
    {{ __('Edit Profile') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
            <div class="card profile-card">
                <div class="icon-user avatar rounded-circle">
                    <img src="{{(!empty($userDetail->avatar))? asset(Storage::url('avatar/'.$userDetail->avatar)) : asset(Storage::url('avatar/avatar.png'))}}" class="icon-user avatar rounded-circle">
                </div>
                <h4 class="h4 mb-0 mt-2">{{$userDetail->name}}</h4>
                <div class="sal-right-card">
                    <span class="badge badge-pill badge-blue">{{$userDetail->type}}</span>
                </div>
                <h6 class="office-time mb-0 mt-4">{{$userDetail->email}}</h6>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
            <section class="col-lg-12 pricing-plan card">
                <div class="our-system password-card p-3">
                    <div class="row">
                        <ul class="nav nav-tabs my-4">
                            <li>
                                <a data-toggle="tab" href="#personal_info" class="active">{{__('Personal info')}}</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#change_password" class="">{{__('Change Password')}}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="personal_info" class="tab-pane in active">
                                {{Form::model($userDetail,array('route' => array('update.account'), 'method' => 'POST', 'enctype' => "multipart/form-data"))}}
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="name" class="form-control-label text-dark">{{__('Name')}}</label>
                                            <input class="form-control @error('name') is-invalid @enderror" name="name" type="text" id="name" placeholder="{{ __('Enter Your Name') }}" value="{{ $userDetail->name }}" autocomplete="name">
                                            @error('name')
                                            <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label text-dark">{{__('Email')}}</label>
                                            <input class="form-control @error('email') is-invalid @enderror" name="email" type="text" id="email" placeholder="{{ __('Enter Your Email Address') }}" value="{{ $userDetail->email }}" autocomplete="email">
                                            @error('email')
                                            <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-dark">{{__('Avatar')}}</label>
                                            <div class="choose-file">
                                                <label for="avatar">
                                                    <div>{{__('Choose file here')}}</div>
                                                    <input class="form-control" name="profile" type="file" id="avatar" accept="image/*" data-filename="profile_update">
                                                </label>
                                                <p class="profile_update"></p>
                                            </div>
                                            @error('avatar')
                                            <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <span class="clearfix"></span>
                                        <span class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <input type="submit" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                                    </div>
                                </div>
                                {{Form::close()}}
                            </div>
                            <div id="change_password" class="tab-pane">
                                {{Form::model($userDetail,array('route' => array('update.password',$userDetail->id), 'method' => 'POST'))}}
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="current_password" class="form-control-label text-dark">{{ __('Current Password') }}</label>
                                        <input class="form-control @error('current_password') is-invalid @enderror" name="current_password" type="password" id="current_password" autocomplete="current_password" placeholder="{{ __('Enter Current Password') }}">
                                        @error('current_password')
                                        <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="new_password" class="form-control-label text-dark">{{ __('Password') }}</label>
                                        <input class="form-control @error('new_password') is-invalid @enderror" name="new_password" type="password" autocomplete="new_password" id="new_password" placeholder="{{ __('Enter New Password') }}">
                                        @error('new_password')
                                        <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="confirm_password" class="form-control-label text-dark">{{ __('Confirm Password') }}</label>
                                        <input class="form-control @error('confirm_password') is-invalid @enderror" name="confirm_password" type="password" autocomplete="confirm_password" id="confirm_password" placeholder="{{ __('Confirm New Password') }}">
                                        @error('confirm_password')
                                        <span class="invalid-feedback text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <input type="submit" value="{{__('Change Password')}}" class="btn-create badge-blue">
                                    </div>
                                </div>
                                {{Form::close()}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
