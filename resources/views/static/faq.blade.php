@extends('layouts.empty')


@section('title')
    netframe :: {{ trans('links.faq') }}
@stop

@section('content')
    <div class="col-xs-12 col-md-6 offset-md-3 pb-5">
        <div class="panel default-panel mb-5">
            <div class="panel-heading">
                <h1 class="widget-title">{{ trans('links.faq') }}</h1>
            </div>
            <div class="panel-body">
                @include('lang.'.Lang::locale().'.faq')
            </div>
        </div>
        @include('static.links')
    </div>
@stop