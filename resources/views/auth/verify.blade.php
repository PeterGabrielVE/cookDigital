@extends('layouts.auth')

@section('title')
    {{ __('Verification') }}
@endsection

@section('content')
    <div class="login-form">
        <div class="page-title"><h5>{{__('Verify Your Email Address')}}</h5></div>
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif
        <span>{{ __('Before proceeding, please check your email for a verification link.') }}</span>
        <span>{{ __('If you did not receive the email') }},</span>
        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn-login align-baseline">{{ __('click here to request another') }}</button>
            .
        </form>
    </div>
@endsection
