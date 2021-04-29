@extends('layouts.admin')
@php
    $profile=asset(Storage::url('avatar/'));
    $logo=asset(Storage::url('logo/'));
@endphp
@section('page-title')
    {{__('Settings')}}
@endsection
@push('script-page')
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{url('/invoices/preview')}}/' + template + '/' + color);
        });

        $(document).on("change", "select[name='estimation_template'], input[name='estimation_color']", function () {
            var template = $("select[name='estimation_template']").val();
            var color = $("input[name='estimation_color']:checked").val();
            $('#estimation_frame').attr('src', '{{url('/estimations/preview')}}/' + template + '/' + color);
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li>
                    <a class="active" id="contact-tab4" data-toggle="tab" href="#site-setting" role="tab" aria-controls="" aria-selected="false">{{__('Site Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#company-setting" role="tab" aria-controls="" aria-selected="false">{{__('Company Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#email-setting" role="tab" aria-controls="" aria-selected="false">{{__('Email Setting')}}</a>
                </li>
                <li>
                    <a id="contact-tab4" data-toggle="tab" href="#system-setting" role="tab" aria-controls="" aria-selected="false">{{__('System Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab4" data-toggle="tab" href="#company-payment-setting" role="tab" aria-controls="" aria-selected="false">{{__('Payment Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab5" data-toggle="tab" href="#pusher-setting" role="tab" aria-controls="" aria-selected="false">{{__('Pusher Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab6" data-toggle="tab" href="#invoice-setting" role="tab" aria-controls="" aria-selected="false">{{__('Invoice Print Setting')}}</a>
                </li>
                <li>
                    <a id="profile-tab7" data-toggle="tab" href="#estimation-setting" role="tab" aria-controls="" aria-selected="false">{{__('Estimation Print Setting')}}</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="site-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    {{Form::open(array('url'=>'systems','method'=>'POST','enctype' => "multipart/form-data"))}}
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title">{{__('Logo')}}</h4>
                            <div class="card setting-card">
                                <div class="logo-content">
                                    <img src="{{ asset(Storage::url('logo/logo.png')) }}" class="big-logo">
                                </div>
                                <div class="input-file btn-file">{{__('Select image')}}
                                    <input type="file" class="form-control" name="logo" id="logo" data-filename="logo_update" accept=".jpeg,.jpg,.png">
                                </div>
                                <p class="logo_update"></p>
                                @error('logo')
                                <span class="invalid-logo text-xs text-danger" role="alert">{{ $message }}</span>
                                @enderror
                                <p class="lh-160 mb-0 pt-3">{{__('These Logo will appear on Estimations and Invoice as well.')}}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title">{{__('Favicon')}}</h4>
                            <div class="card setting-card">
                                <div class="logo-content">
                                    <img src="{{ asset(Storage::url('logo/favicon.png')) }}" class="small-logo">
                                </div>
                                <div class="input-file btn-file">{{__('Select image')}}
                                    <input type="file" class="form-control" name="favicon" id="favicon" data-filename="favicon_update" accept=".jpeg,.jpg,.png">
                                </div>
                                <p class="favicon_update"></p>
                                @error('favicon')
                                <span class="invalid-favicon text-xs text-danger" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title">{{__('Landing Page Logo')}}</h4>
                            <div class="card setting-card">
                                <div class="logo-content">
                                    <img src="{{ asset(Storage::url('logo/landing_logo.png')) }}" class="big-logo">
                                </div>
                                <div class="input-file btn-file">{{__('Select image')}}
                                    <input type="file" class="form-control" name="landing_logo" id="landing_logo" data-filename="landing_logo_update" accept=".jpeg,.jpg,.png">
                                </div>
                                <p class="landing_logo_update"></p>
                                @error('landing_logo')
                                <span class="invalid-landing_logo text-xs text-danger" role="alert">{{ $message }}</span>
                                @enderror
                                <div class="form-group">
                                    <br>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="enable_landing" value="yes" class="custom-control-input" id="enable_landing" {{ (Utility::getValByName('enable_landing') == 'yes') ? 'checked' : '' }}>
                                        <label class="custom-control-label font-weight-bold text-dark text-xs" for="enable_landing">{{ __('Enable Landing Page') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title">{{__('Settings')}}</h4>
                            <div class="card setting-card">
                                <div class="form-group">
                                    {{Form::label('header_text',__('Title Text'),['class'=>'form-control-label']) }}
                                    {{Form::text('header_text',\App\Utility::getValByName('header_text'),array('class'=>'form-control','placeholder'=>__('Header Text')))}}
                                    @error('header_text')
                                    <span class="invalid-header_text text-xs text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{Form::label('footer_text',__('Footer Text'),['class'=>'form-control-label']) }}
                                    {{Form::text('footer_text',\App\Utility::getValByName('footer_text'),array('class'=>'form-control','placeholder'=>__('Footer Text')))}}
                                    @error('footer_text')
                                    <span class="invalid-footer_text text-xs text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{Form::label('default_language',__('Default Language'),['class'=>'form-control-label']) }}
                                    <select name="default_language" id="default_language" class="form-control select2">
                                        @foreach(Utility::languages() as $language)
                                            <option @if(Utility::getValByName('default_language') == $language) selected @endif value="{{$language}}">{{Str::upper($language)}}</option>
                                        @endforeach
                                    </select>
                                    @error('default_language')
                                    <span class="invalid-default_language text-danger text-xs" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                                </div>
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <div class="card bg-none">
                        {{Form::model($settings,array('route'=>'company.settings','method'=>'post'))}}
                        <div class="row company-setting">
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_name *',__('Company Name *'),['class'=>'form-control-label']) }}
                                {{Form::text('company_name',null,array('class'=>'form-control font-style'))}}
                                @error('company_name')
                                <span class="text-xs text-danger invalid-company_name" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_address',__('Address'),['class'=>'form-control-label']) }}
                                {{Form::text('company_address',null,array('class'=>'form-control font-style'))}}
                                @error('company_address')
                                <span class="text-xs text-danger invalid-company_address" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_city',__('City'),['class'=>'form-control-label']) }}
                                {{Form::text('company_city',null,array('class'=>'form-control font-style'))}}
                                @error('company_city')
                                <span class="text-xs text-danger invalid-company_city" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_state',__('State'),['class'=>'form-control-label']) }}
                                {{Form::text('company_state',null,array('class'=>'form-control font-style'))}}
                                @error('company_state')
                                <span class="text-xs text-danger invalid-company_state" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_zipcode',__('Zip/Post Code'),['class'=>'form-control-label']) }}
                                {{Form::text('company_zipcode',null,array('class'=>'form-control'))}}
                                @error('company_zipcode')
                                <span class="text-xs text-danger invalid-company_zipcode" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_country',__('Country'),['class'=>'form-control-label']) }}
                                {{Form::text('company_country',null,array('class'=>'form-control font-style'))}}
                                @error('company_country')
                                <span class="text-xs text-danger invalid-company_country" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_telephone',__('Telephone'),['class'=>'form-control-label']) }}
                                {{Form::text('company_telephone',null,array('class'=>'form-control'))}}
                                @error('company_telephone')
                                <span class="text-xs text-danger invalid-company_telephone" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_email',__('System Email *'),['class'=>'form-control-label']) }}
                                {{Form::text('company_email',null,array('class'=>'form-control'))}}
                                @error('company_email')
                                <span class="text-xs text-danger invalid-company_email" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('company_email_from_name',__('Email (From Name) *'),['class'=>'form-control-label']) }}
                                {{Form::text('company_email_from_name',null,array('class'=>'form-control font-style'))}}
                                @error('company_email_from_name')
                                <span class="text-xs text-danger invalid-company_email_from_name" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
                <div class="tab-pane fade" id="email-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    {{Form::open(array('route'=>'email.settings','method'=>'post'))}}
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_driver',__('Mail Driver'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_driver',env('MAIL_DRIVER'),array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))}}
                                @error('mail_driver')
                                <span class="text-xs text-danger invalid-mail_driver" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_host',__('Mail Host'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_host',env('MAIL_HOST'),array('class'=>'form-control ','placeholder'=>__('Enter Mail Driver')))}}
                                @error('mail_host')
                                <span class="text-xs text-danger invalid-mail_driver" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_port',__('Mail Port'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_port',env('MAIL_PORT'),array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))}}
                                @error('mail_port')
                                <span class="text-xs text-danger invalid-mail_port" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_username',__('Mail Username'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_username',env('MAIL_USERNAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))}}
                                @error('mail_username')
                                <span class="text-xs text-danger invalid-mail_username" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_password',__('Mail Password'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_password',env('MAIL_PASSWORD'),array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))}}
                                @error('mail_password')
                                <span class="text-xs text-danger invalid-mail_password" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_encryption',__('Mail Encryption'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_encryption',env('MAIL_ENCRYPTION'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                @error('mail_encryption')
                                <span class="text-xs text-danger invalid-mail_encryption" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_from_address',__('Mail From Address'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_from_address',env('MAIL_FROM_ADDRESS'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))}}
                                @error('mail_from_address')
                                <span class="text-xs text-danger invalid-mail_from_address" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('mail_from_name',__('Mail From Name'),['class'=>'form-control-label']) }}
                                {{Form::text('mail_from_name',env('MAIL_FROM_NAME'),array('class'=>'form-control','placeholder'=>__('Enter From Name')))}}
                                @error('mail_from_name')
                                <span class="text-xs text-danger invalid-mail_from_name" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 text-right">
                                <a href="#" class="btn btn-xs btn-white btn-icon-only bg-warning width-auto" data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" data-url="{{route('test.email')}}">
                                    {{__('Test Mail')}}
                                </a>
                                {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    {{Form::model($settings,array('route'=>'system.settings','method'=>'post'))}}
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                {{Form::label('site_currency',__('Currency *'),['class'=>'form-control-label']) }}
                                {{Form::text('site_currency',null,array('class'=>'form-control font-style'))}}
                                <small>
                                    {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                    <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                </small>
                                @error('site_currency')
                                <span class="text-xs text-danger invalid-site_currency" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                {{Form::label('site_currency_symbol',__('Currency Symbol *'),['class'=>'form-control-label']) }}
                                {{Form::text('site_currency_symbol',null,array('class'=>'form-control'))}}
                                @error('site_currency_symbol')
                                <span class="invalid-site_currency_symbol" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label class="form-control-label">{{__('Currency Symbol Position')}}</label>
                                <div class="d-flex radio-check">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="pre" value="pre" name="site_currency_symbol_position" class="custom-control-input" @if($settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                        <label class="custom-control-label form-control-label" for="pre">{{__('Pre')}}</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="post" value="post" name="site_currency_symbol_position" class="custom-control-input" @if($settings['site_currency_symbol_position'] == 'post') checked @endif>
                                        <label class="custom-control-label form-control-label" for="post">{{__('Post')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="site_date_format" class="form-control-label">{{__('Date Format')}}</label>
                                <select type="text" name="site_date_format" class="form-control" id="site_date_format">
                                    <option value="M j, Y" @if(@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015</option>
                                    <option value="d-m-Y" @if(@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>d-m-y</option>
                                    <option value="m-d-Y" @if(@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>m-d-y</option>
                                    <option value="Y-m-d" @if(@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>y-m-d</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="site_time_format" class="form-control-label">{{__('Time Format')}}</label>
                                <select type="text" name="site_time_format" class="form-control" id="site_time_format">
                                    <option value="g:i A" @if(@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM</option>
                                    <option value="g:i a" @if(@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm</option>
                                    <option value="H:i" @if(@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                {{Form::label('invoice_prefix',__('Invoice Prefix'),['class'=>'form-control-label']) }}
                                {{Form::text('invoice_prefix',null,array('class'=>'form-control'))}}
                                @error('invoice_prefix')
                                <span class="text-xs text-danger invalid-invoice_prefix" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                {{Form::label('bug_prefix',__('Bug Prefix'),['class'=>'form-control-label']) }}
                                {{Form::text('bug_prefix',null,array('class'=>'form-control'))}}
                                @error('bug_prefix')
                                <span class="text-xs text-danger invalid-bug_prefix" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                {{Form::label('estimation_prefix',__('Estimation Prefix'),['class'=>'form-control-label']) }}
                                {{Form::text('estimation_prefix',null,array('class'=>'form-control'))}}
                                @error('estimation_prefix')
                                <span class="text-xs text-danger invalid-estimation_prefix" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="footer_title" class="form-control-label">{{__('Invoice/Estimation Title')}}  </label>
                                <input type="text" name="footer_title" class="form-control" id="footer_title" value="{{$settings['footer_title']}}">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="footer_note" class="form-control-label">{{__('Invoice/Estimation Note')}}</label>
                                <textarea name="footer_note" id="footer_note" class="form-control">{{$settings['footer_note']}}</textarea>
                            </div>
                            <div class="form-group col-md-12 text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="company-payment-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    {{Form::model($settings,['route'=>'company.payment.settings', 'method'=>'POST'])}}
                    <div class="card company-setting">
                        <div class="col-md-12">
                            <small class="text-dark font-weight-bold">{{__("This detail will use for collect payment on invoice from clients. On invoice client will find out pay now button based on your below configuration.")}}</small>
                            <div class="row">
                                <ul class="nav nav-tabs my-4" role="tablist">
                                    <li>
                                        <a class="active" id="stripe-setting" data-toggle="tab" href="#stripe-settings" role="tab" aria-controls="home" aria-selected="true">{{ __('Stripe') }}</a>
                                    </li>
                                    <li>
                                        <a id="paypal-setting" data-toggle="tab" href="#paypal-settings" role="tab" aria-controls="profile" aria-selected="false">{{ __('Paypal') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="stripe-settings" role="tabpanel" aria-labelledby="stripe-setting">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-control-label">{{ __('Enable Stripe') }}</label>
                                            <label class="switch ml-3">
                                                <input type="checkbox" name="site_enable_stripe" {{ $settings['site_enable_stripe'] == 'on' ? 'checked="checked"' : '' }}>
                                                <span class="slider1 round"></span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="stripe_key" class="form-control-label">{{__('Stripe Key')}}</label>
                                            <input class="form-control {{ ($errors->has('site_stripe_key')) ? 'is-invalid' : '' }}" name="site_stripe_key" type="text" value="{{ $settings['site_stripe_key'] }}" id="stripe_key" placeholder="{{ __('Stripe Key') }}">
                                            @if ($errors->has('site_stripe_key'))
                                                <span class="invalid-feedback d-block text-xs">
                                                        {{ $errors->first('site_stripe_key') }}
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="stripe_secret" class="form-control-label">{{__('Stripe Secret')}}</label>
                                            <input class="form-control {{ ($errors->has('site_stripe_secret')) ? 'is-invalid' : '' }}" name="site_stripe_secret" type="text" value="{{ $settings['site_stripe_secret'] }}" id="stripe_secret" placeholder="{{ __('Stripe Secret') }}">
                                            @if ($errors->has('site_stripe_secret'))
                                                <span class="invalid-feedback d-block text-xs">
                                                        {{ $errors->first('site_stripe_secret') }}
                                                    </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="paypal-settings" role="tabpanel" aria-labelledby="paypal-setting">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">{{ __('Enable Paypal') }}</label>
                                                <label class="switch ml-3">
                                                    <input type="checkbox" name="site_enable_paypal" {{ $settings['site_enable_paypal'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <span class="slider1 round"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="paypal-label form-control-label" for="paypal_mode">{{__('Paypal Mode')}}</label>
                                            <div class="d-flex radio-check">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="sandbox" value="sandbox" name="site_paypal_mode" class="custom-control-input" {{ $settings['site_paypal_mode'] == '' || $settings['site_paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="sandbox">{{__('Sandbox')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="live" value="live" name="site_paypal_mode" class="custom-control-input" {{ $settings['site_paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="live">{{__('Live')}}</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="paypal_client_id" class="form-control-label">{{ __('Client ID') }}</label>
                                                <input type="text" name="site_paypal_client_id" id="paypal_client_id" class="form-control {{ ($errors->has('site_paypal_client_id')) ? 'is-invalid' : '' }}" value="{{ $settings['site_paypal_client_id'] }}" placeholder="{{ __('Client ID') }}"/>
                                                @if ($errors->has('site_paypal_client_id'))
                                                    <span class="invalid-feedback d-block text-xs">
                                                        {{ $errors->first('site_paypal_client_id') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="paypal_secret_key" class="form-control-label">{{ __('Secret Key') }}</label>
                                                <input type="text" name="site_paypal_secret_key" id="paypal_secret_key" class="form-control {{ ($errors->has('site_paypal_secret_key')) ? 'is-invalid' : '' }}" value="{{ $settings['site_paypal_secret_key'] }}" placeholder="{{ __('Secret Key') }}"/>
                                                @if ($errors->has('site_paypal_secret_key'))
                                                    <span class="invalid-feedback d-block text-xs">
                                                        {{ $errors->first('site_paypal_secret_key') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12 text-right">
                            <input type="submit" id="save-btn" value="{{__('Save Changes')}}" class="btn-create badge-blue">
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="pusher-setting" role="tabpanel" aria-labelledby="contact-tab5">
                    {{Form::open(array('route'=>'pusher.settings','method'=>'post'))}}
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="enable_chat" value="yes" class="custom-control-input" id="enable_chat" @if(env('CHAT_MODULE') =='yes') checked @endif>
                                        <label class="custom-control-label font-weight-bold text-dark text-sm" for="enable_chat">{{ __('Enable Chat') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('pusher_app_id',__('Pusher App Id'),['class'=>'form-control-label']) }}
                                {{Form::text('pusher_app_id',env('PUSHER_APP_ID'),array('class'=>'form-control','placeholder'=>__('Enter Pusher App Id')))}}
                                @error('pusher_app_id')
                                <span class="text-xs text-danger invalid-pusher_app_id" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('pusher_app_key',__('Pusher App Key'),['class'=>'form-control-label']) }}
                                {{Form::text('pusher_app_key',env('PUSHER_APP_KEY'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Key')))}}
                                @error('pusher_app_key')
                                <span class="text-xs text-danger invalid-pusher_app_key" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('pusher_app_secret',__('Pusher App Secret'),['class'=>'form-control-label']) }}
                                {{Form::text('pusher_app_secret',env('PUSHER_APP_SECRET'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Secret')))}}
                                @error('pusher_app_secret')
                                <span class="text-xs text-danger invalid-pusher_app_secret" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                {{Form::label('pusher_app_cluster',__('Pusher App Cluster'),['class'=>'form-control-label']) }}
                                {{Form::text('pusher_app_cluster',env('PUSHER_APP_CLUSTER'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Cluster')))}}
                                @error('pusher_app_cluster')
                                <span class="text-xs text-danger invalid-pusher_app_cluster" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group col-md-12 text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                            </div>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
                <div class="tab-pane fade" id="invoice-setting" role="tabpanel" aria-labelledby="profile-tab6">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="{{route('template.setting')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="address" class="form-control-label">{{__('Invoice Template')}}</label>
                                        <select class="form-control select2" name="invoice_template">
                                            @foreach(Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{$key}}" {{(isset($settings['invoice_template']) && $settings['invoice_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Color Input')}}</label>
                                        <div class="row gutters-xs">
                                            @foreach(Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="invoice_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['invoice_color']) && $settings['invoice_color'] == $color) ? 'checked' : ''}}>
                                                        <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Invoice Logo')}}</label>
                                        <div class="choose-file form-group">
                                            <label for="invoice_logo" class="form-control-label">
                                                <div>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control" name="invoice_logo" id="invoice_logo" data-filename="invoice_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="invoice_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-10">
                                @if(isset($settings['invoice_template']) && isset($settings['invoice_color']))
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',[$settings['invoice_template'],$settings['invoice_color']])}}"></iframe>
                                @else
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="{{route('invoice.preview',['template1','fffff'])}}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="estimation-setting" role="tabpanel" aria-labelledby="profile-tab7">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="{{route('template.setting')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="address" class="form-control-label">{{__('Estimation Template')}}</label>
                                        <select class="form-control select2" name="estimation_template">
                                            @foreach(Utility::templateData()['templates'] as $key => $template)
                                                <option value="{{$key}}" {{(isset($settings['estimation_template']) && $settings['estimation_template'] == $key) ? 'selected' : ''}}>{{$template}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Color Input')}}</label>
                                        <div class="row gutters-xs">
                                            @foreach(Utility::templateData()['colors'] as $key => $color)
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="estimation_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['estimation_color']) && $settings['estimation_color'] == $color) ? 'checked' : ''}}>
                                                        <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">{{__('Estimation Logo')}}</label>
                                        <div class="choose-file form-group">
                                            <label for="estimation_logo" class="form-control-label">
                                                <div>{{__('Choose file here')}}</div>
                                                <input type="file" class="form-control" name="estimation_logo" id="estimation_logo" data-filename="estimation_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="estimation_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="{{__('Save')}}" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-10">
                                @if(isset($settings['estimation_template']) && isset($settings['estimation_color']))
                                    <iframe id="estimation_frame" frameborder="0" class="w-100 h-1050" src="{{route('estimations.preview',[$settings['estimation_template'],$settings['estimation_color']])}}"></iframe>
                                @else
                                    <iframe id="estimation_frame" frameborder="0" class="w-100 h-1050" src="{{route('estimations.preview',['template1','fffff'])}}"></iframe>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
