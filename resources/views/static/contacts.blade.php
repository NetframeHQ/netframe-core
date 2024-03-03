@extends('layouts.empty')

@section('title')
    {{ trans('links.contacts') }}
@stop


@section('content')
    <div class="text-center">
        @include('lang.' . Lang::locale() . '.contacts')
    </div>
@stop