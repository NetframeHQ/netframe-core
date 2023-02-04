
@extends('accountent.layout-simple')

@section('content')
<div class="col-md-4"></div>
<div class="col-md-4">
    <hr />
    <div class="jumbotron">
        {{ Form::open(['action' => ['Accountent\AuthController@remindPassword', $tokenPassword], 'role' => 'form']) }}

        @if(session()->has('login_errors'))
        <div class="alert alert-danger">{{ session('login_errors') }}</div>
        @endif

        <div class="form-group">
            {{ Form::label('password', trans('auth.newPassPass')) }}
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-lock"></i>
                </div>
                <input type="password" class="form-control" name="password"> 
            </div>
            
                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
        </div>

        <div class="form-group">
            {{ Form::label('password_confirmation', trans('auth.newPassConfirm')) }}
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-lock"></i>
                </div>
                <input type="password" class="form-control" name="password_confirmation">
            </div>
                {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}
        </div>

        <button type="submit" class="btn btn-primary btn-block">{{ trans('form.send') }}</button>
        {{ Form::close() }}
    </div>
</div>
<div class="col-md-4"></div>
@stop