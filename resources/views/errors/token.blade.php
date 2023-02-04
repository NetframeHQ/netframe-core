@extends('layouts.error')

@section('content')
    <div class="text-center">
        {!! trans('error.token') !!}
        <br><br>
        <a href="{{ url()->previous() }}">> {{ trans('error.back') }} <</a>
    </div>
@stop