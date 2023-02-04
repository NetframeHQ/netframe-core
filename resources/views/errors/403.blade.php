@extends('layouts.error')

@section('content')
    <div class="text-center">
        {{ trans('error.403') }}
        <br /><br />
        <a href="{{ url()->route('home') }}">{{ trans('error.backHome') }}</a>
    </div>
@stop