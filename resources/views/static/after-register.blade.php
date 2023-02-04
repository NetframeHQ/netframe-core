@extends('layouts.master')


@section('content')
    <div class="col-xs-12 col-md-6 offset-md-3">
        <div class="panel default-panel">
            <div class="panel-heading">
                <h1 class="widget-title">{{ trans('auth.afterRegisterStep'.$step) }}</h1>
            </div>
            <div class="panel-body">
                @include('lang.'.Lang::locale().'.after-register-'.$step)
            </div>
        </div>
        @include('static.links')
    </div>
@stop