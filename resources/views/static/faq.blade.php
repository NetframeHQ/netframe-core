@extends('layouts.empty')


@section('title')
    netframe :: {{ trans('links.faq') }}
@stop

@section('content')
    <div class="col-xs-12 col-md-6 offset-md-3 pb-5">
        <div class="panel default-panel mb-5">
            <div class="panel-heading d-flex flex-column">
                <div class="mb-5 mt-4">
                    <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
                    <img src="{{ asset('assets/img/widget-logo-dark.png') }}" class="img-fluid center-block menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
                </div>
                <h1 class="widget-title">{{ trans('links.faq') }}</h1>
            </div>
            <div class="panel-body p-4">
                @include('lang.'.Lang::locale().'.faq')
            </div>
        </div>
        @include('static.links')
    </div>
@stop