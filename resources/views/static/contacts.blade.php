@extends('layouts.empty')


@section('title')
    Netframe :: {{ trans('links.contacts') }}
@stop

@section('content')
    <div class="col-xs-12 col-md-8 offset-md-2 pb-5">
        <div class="panel default-panel mb-5">
            <div class="panel-heading">
                <h1 class="widget-title">{{ trans('links.contacts') }}</h1>
            </div>
            <div class="panel-body">
                @include('lang.'.Lang::locale().'.contacts')
            </div>
        </div>
        @include('static.links')
    </div>
@stop