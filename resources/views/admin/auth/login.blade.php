@extends('admin.layout-simple')

@section('content')
<div class="col-md-4"></div>
<div class="col-md-4">
    <hr />
    <div class="jumbotron">
        {{ Form::open(['route' => 'management.login', 'role' => 'form']) }}

        @if(session()->has('login_errors'))
        <div class="alert alert-danger">{{ session('login_errors') }}</div>
        @endif

        <div class="form-group">
            {{ Form::label('email', 'Email') }}
            {{ Form::text('email', request()->old('email'), ['class' => 'form-control']) }}
        </div>

        <div class="form-group">
            {{ Form::label('password', trans('auth.label_password')) }}
            {{ Form::password('password', ['class' => 'form-control'] ) }}
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