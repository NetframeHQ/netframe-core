@extends('layouts.empty-background')


@section('content')
<div class="login-form-wrapper boarding">
{{ Form::open(['id' => 'auth_login', 'role' => 'form']) }}
    <div class="login-container">
        <div class="login-visual">
            @if(isset($instanceLogo))
                <img src="{{ $instanceLogo }}" class="img-fluid center-block">
            @else
                <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block">
            @endif
        </div>

        <div class="login-form">
            <div class="login-title">{{ trans('boarding.titles.join') }}</class=>
            {!! trans('boarding.joinIntro') !!}
            @if(isset($messageLogin))
                {{ trans($messageLogin) }}
            @endif

            @if(session()->has('login_errors'))
            <div class="alert alert-danger">{{ session('login_errors') }}</div>
            @endif

            <div class="form-group">
                {{ Form::label('password', trans('auth.label_password_login')) }}
                {{ Form::password('password', ['class' => 'form-control'] ) }}
            </div>

            @if(!auth()->guard('web')->check())
                <div class="form-group">
                    <label for="remember_token">
                        {{ Form::checkbox('remember_token', 1, true, ['class'=>'custom-input']) }}
                        {{ trans('auth.remember_me') }}
                    </label>
                </div>
            @endif
        </div>
    </div>

    <button type="submit" class="login-submit">{{ trans('auth.btn_login') }}</button>
{{ Form::close() }}
</div>

@stop