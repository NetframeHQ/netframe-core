@extends('layouts.empty-background')


@section('content')
    <div class="login-form-wrapper">
        {{ Form::open() }}
        <div class="login-container">
            <div class="login-visual">
                @if(isset($instanceLogo))
                    <img src="{{ $instanceLogo }}" class="img-fluid center-block">
                @else
                    <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block">
                @endif
            </div>
            <div class="login-form">
                @if(!isset($error))
                    <p>
                        {{ trans('channels.visio.external.joinTxt') }}
                    </p>
                    <div class="form-group">
                        {{ Form::label('lastname', trans('channels.visio.external.lastname')) }}
                        {{ Form::text('lastname', (($access->lastname != null) ? $access->lastname : ''), ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => '...']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('firstname', trans('channels.visio.external.firstname')) }}
                        {{ Form::text('firstname', (($access->firstname != null) ? $access->firstname: ''), ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => '...']) }}
                    </div>
                    <div class="form-group">
                        {{ Form::label('email', trans('channels.visio.external.email')) }}
                        {{ Form::email('email', (($access->email != null) ? $access->email: ''), ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => '...']) }}
                    </div>
                @else
                    <p>
                        {{ trans('channels.visio.external.errorAccess') }}
                    </p>
                @endif
        </div>
    </div>
    @if(!isset($error))
        <input type="submit" class="login-submit">
    @endif
{{ Form::close() }}
@stop