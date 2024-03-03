@extends('layouts.empty-background')

@section('cssbackground')
    @if(isset($customBackground)) {!! $customBackground !!} @endif
@stop

@section('content')
<div class="login-form-wrapper">
{{ Form::open(['action' =>'AuthController@forgotPassword', 'id' => 'auth-forgotpassword', 'role' => 'form']) }}
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
            <div class="login-title">{{ trans('auth.forgotPassword') }}</div>

            @if(isset($success))
                <div class="card-body">
                    <p>Votre nouveau mot de passe vous est envoyé à cette adresse email :
                        <span class="label label-success">{{ $emailSending }}</span>
                    </p>
                    <p>
                        Le lien sera valide {{ config('auth.timeout_password') }} heures
                    </p>
                </div>
            @else
                <div class="form-group">
                    {{ Form::label('name', trans('auth.label_email')) }}
                    <div class="form-group">
                        <input type="text" class="form-control @if ($errors->has('email')) is-invalid @endif" name="email" value="{{ request()->old('email') }}" placeholder="{{ trans('auth.placeholderMail') }}">
                    </div>
                    {!! $errors->first('email', '<p class="input__error mb-0 mt-2 ft-600"><img src="'.asset('assets/img/boarding/alert-circle.svg').'" alt="icon erreur" class="is-inline" /> ' . trans('auth.msg_badlogin') . '</p>') !!}
                </div>
            @endif
        </div>
    </div>

    @if(!isset($success))
        <button type="submit" class="login-submit">{{ trans('form.send') }}</button>
    @endif
    {{ Form::close() }}
</div>
@include('static.links')
@stop