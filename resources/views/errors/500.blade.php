@extends('layouts.error')

@section('content')
    <div class="text-center">
        {{ trans('error.500') }}
        <br><br>
        <a href="{{ url()->route('home') }}">{{ trans('error.backHome') }}</a>
        <br><br>
        <a href="{{ url()->route('auth.logout') }}">{{ trans('error.relog') }}</a>
    </div>
@stop