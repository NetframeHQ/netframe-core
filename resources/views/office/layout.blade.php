@extends('layouts.master')
@section('title')
    {{ trans('office.title') }} • {{ $globalInstanceName }}
@stop
@section('stylesheets')
    @parent
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/office.css')}}">
@stop

@section('content')
<div class="main-header">
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.tasks_big')
        </span>
        <h2 class="main-header-title">
            <a href="{{route('office.home')}}">{{ trans('office.title') }}</a>
        </h2>
    </div>
</div>
@yield('subcontent')
@endsection
@section('javascripts')
@parent
<script type="text/javascript" src="{{asset('assets/js/office.js')}}"></script>
@yield('subscripts')
@endsection