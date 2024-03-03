@extends('layouts.empty')

@section('title')
    {{ trans('links.cgv') }}
@stop


@section('content')
    @include('lang.' . Lang::locale() . '.cgv')
@stop