@extends('layouts.auth')

@section('title')
    {{ __('Confirm Password') }}
@endsection

@section('content')
    <div class="login-form">
        <div class="page-title"><h5>{{__('Confirm Password')}}</h5></div>
        <span>{{ __('Please confirm your password before continuing.') }}</span>
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="form-group">
                <label for="password" class="form-control-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <small>{{ $message }}</small>
                </span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn-login">
                    {{ __('Confirm Password') }}
                </button>
                @if (Route::has('password.request'))
                    <a class="text-xs text-muted text-center" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
@endsection
