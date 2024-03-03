@extends('layouts.empty-background')

@section('cssbackground')
    @if(isset($customBackground)) {!! $customBackground !!} @endif
@stop

@section('content')
<div class="login-form-wrapper">
{{ Form::open(['id' => 'auth_login', 'role' => 'form']) }}
    @if(isset($duuid))
        {{ Form::hidden('duuid', $duuid) }}
    @endif

    <div class="login-container">
        <div class="login-visual">
            @if(isset($instanceLogo))
                <img src="{{ $instanceLogo }}" class="img-fluid center-block menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
            @else
                <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
            @endif

            @if(isset($instanceLogoDark))
                <img src="{{ $instanceLogoDark }}" class="img-fluid center-block menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
            @else
                <img src="{{ asset('assets/img/widget-logo-dark.png') }}" class="img-fluid center-block menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
            @endif
        </div>

        <div class="login-form">
                @if(session()->has('login_errors'))
                    <div class="alert alert-danger">
                        {{ session('login_errors') }}

                        @if(session()->has('resend_boarding_link'))
                            <br>
                            <div class="text-center">
                                <a href="{{ env('APP_URL') }}/boarding/resent-link" class="button">{{ trans('boarding.resendLink') }}</a>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($messageLogin))
                    <div class="alert alert-danger">{{ trans('auth.messageLogin.'.$messageLogin) }}</div>
                @endif

                <div class="form-group">
                    {{ Form::label('email', trans('auth.label_email')) }}
                    {{ Form::email('email', $email, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '...']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('password', trans('auth.label_password_login')) }}
                    {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '...'] ) }}
                </div>

                <div class="form-element login-foot d-flex justify-content-between">
                    <label for="remember_token">
                        <div class="nf-checkbox">
                            {{ Form::checkbox('remember_token', 1, true, ['class'=>'', 'id' => 'remember_token']) }}
                        </div>
                        {{ trans('auth.remember_me') }}
                    </label>

                    <a href="{{ url()->route('auth.forgotPassword') }}" title="{{ trans('auth.forgot_password') }}" class="reset-password">
                        {{ trans('auth.forgot_password') }}
                    </a>
                </div>
                @if(!session()->has('instanceId'))
                    <p>
                        <a href="{{ url()->route('auth.register') }}" title="{{ trans('auth.btn_signin') }}">
                            {{ trans('auth.btn_signin') }}
                        </a>
                    </p>
                 @endif
        </div>
    </div>
    <button type="submit" class="login-submit">{{ trans('auth.btn_login') }}</button>
{{ Form::close() }}
</div>
@include('static.links')
@stop