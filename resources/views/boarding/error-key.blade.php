@extends('layouts.empty-background')


@section('content')

<div class="login-form-wrapper boarding">
    <div class="login-container">
        <div class="login-visual">
            @if(isset($instanceLogo))
                <img src="{{ $instanceLogo }}" class="img-fluid center-block">
            @else
                <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block">
            @endif
        </div>

        <div class="login-form">
            <div class="login-title">{{ trans('boarding.titles.errorLink') }}</div>
            @if(isset($errorCode))
                <p class="text-danger text-center">
                    {{ trans('boarding.error.'.$errorCode) }}
                </p>
            @endif

            <p>
                {{ trans('boarding.goLogin.text') }}
            </p>
            <p class="text-center">
                <a href="{{ url()->route('login') }}" class="btn btn-border-default">
                    {{ trans('boarding.goLogin.link') }}
                </a>
            </p>
        </div>
    </div>
    @include('static.links')
</div>
@stop