@extends('admin.layout')


@section('content')
<div class="col-md-12">
    <h1 class="Hn-title">{{ trans('admin.titles.updatePass') }} : {{ $user->getNameDisplay() }}</h1>
    @if($result == 'passUpdated')
        <div class="bg-success text-center">{{ trans('admin.detail.passUpdated') }}</div>
    @endif
    {{ Form::open() }}
        <div class="form-group">
            {{ Form::label('password', trans('admin.detail.password')) }}
            {{ Form::text('password', '', ['class' => 'form-control']) }}
        </div>
        <div class="text-center">
            {{ Form::submit() }}
        </div>
    {{ Form::close() }}

</div>

@stop