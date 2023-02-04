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
            <div class="login-title">{{ trans('boarding.titles.resent') }}</div>
            <p>
                {!! trans('boarding.txtResent') !!}
            </p>
        </div>
    </div>

</div>
@stop