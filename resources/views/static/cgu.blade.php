@extends('layouts.empty')

@section('title')
    {{ trans('links.cgu') }}
@stop


@section('content')
    @include('lang.' . Lang::locale() . '.cgu')
@stop