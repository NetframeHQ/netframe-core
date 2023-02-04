@extends('accountent.layout-simple')

@section('content')
<div class="col-md-4"></div>
<div class="col-md-4">
    <hr />
    <div class="jumbotron">
        {{ Form::open(['route' => 'accountent.login', 'role' => 'form']) }}

        @if(session()->has('login_errors'))
        <div class="alert alert-danger">{{ session('login_errors') }}</div>
        @endif

        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-envelope alert-info"></i>
                </div>
                {{ Form::text('email', request()->old('email'), ['class' => 'form-control']) }}    
            </div>
            
        </div>

        <div class="form-group">
            {{ Form::label('password', trans('auth.label_password')) }}
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-lock alert-info"></i>
                </div>
                {{ Form::password('password', ['class' => 'form-control'] ) }}
            </div>
        </div>

        <div class="form-group">
            <label>{{ Form::checkbox('remember_me', 1) }} {{ trans('auth.remember_me') }}</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block">{{ trans('auth.btn_login') }}</button>
        {{ Form::close() }}
    </div>
</div>
<div class="col-md-4"></div>
@stop