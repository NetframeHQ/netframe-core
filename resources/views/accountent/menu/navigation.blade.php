@extends('accountent.layout')


@section('content')


    <div class="col-md-11 mg-bottom">
        <h1 class="projectTitle">{{ trans('accountent.title') }}</h1>
    </div>

    <div class="col-md-3">
        <div class="panel panel-default sidebar">
            <div class="panel-body sidebar-nav navbar-collapse">
                <ul class="nav">
                    <li>
                        <a href="{{ url()->route('accountent.home') }}">
                            {{ trans('accountent.menu.bills') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ url()->route('accountent.paymentinfos') }}">
                            {{ trans('accountent.menu.payMode') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ url()->route('accountent.infos') }}">
                            {{ trans('accountent.menu.infos') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ url()->route('accountent.logout') }}">
                            {{ trans('accountent.menu.logout') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">

                <!--<div id="fn-instance" class="alert hide" role="alert"></div>-->
                @yield('subcontent')

            </div>
        </div>

    </div>
@stop