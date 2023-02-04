@extends('layouts.empty-background')

@section('cssbackground')
    @if(isset($customBackground)) {!! $customBackground !!} @endif
@stop

@section('content')
<div class="login-form-wrapper">
{{ Form::open(['action' => ['AuthController@remindPassword', $tokenPassword], 'role' => 'form']) }}
    <div class="login-container">
        <div class="login-visual">
            @if(isset($instanceLogo))
                <img src="{{ $instanceLogo }}" class="img-fluid center-block">
            @else
                <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block">
            @endif
        </div>

        <div class="login-form">
            <div class="login-title">{{ trans('auth.newPassTitle') }}</div>
            @if($timeOver)
                <p>{{ trans('auth.overtime') }}</p>
            @else
                <div class="form-group">
                    {{ Form::label('password', trans('auth.newPassPass')) }}
                    <input type="password" class="form-control @if ($errors->has('password')) has-error @endif" name="password" id="password">
                    {!! $errors->first('password', '<p class="invalid-feedback">:message</p>') !!}
                </div>

                <div class="form-group">
                    {{ Form::label('password_confirmation', trans('auth.newPassConfirm')) }}
                    <input type="password" class="form-control @if ($errors->has('password_confirmation')) has-error @endif" name="password_confirmation" id="password_confirmation">
                    {!! $errors->first('password_confirmation', '<p class="invalid-feedback">:message</p>') !!}
                </div>

            @endif
        </div>
    </div>

    <button type="submit" class="login-submit">{{ trans('form.send') }}</button>
{{ Form::close() }}

@include('static.links')
</div>
@stop