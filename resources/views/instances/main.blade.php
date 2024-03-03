@extends('layouts.master-header')

@section('title')
    {{ trans('instances.parameters') }} • {{ $globalInstanceName }}
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.settings_big')
        </span>
        <h1 class="main-header-title">{{ trans('instances.parameters') }}</h1>
    </div>
@stop

@section('content')
    <div id="nav_skipped" class="main-scroller">
        <!--<div id="fn-instance" class="alert hide" role="alert"></div>-->
        <div class="nf-settings">
            <div class="nf-settings-sidebar">
                <ul class="nf-settings-links">
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.boarding')) ? 'active' : '' }}" href="{{ url()->route('instance.boarding') }}">
                            {{ trans('instances.menu.boarding') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.graphical')) ? 'active' : '' }}" href="{{ url()->route('instance.graphical') }}">
                            {{ trans('instances.menu.graphical') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.apps')) ? 'active' : '' }}" href="{{ url()->route('instance.apps') }}">
                            {{ trans('instances.menu.apps') }}
                        </a>
                    </li>
                    <li class="sep sep-half"></li>

                    @if(!session('instanceMonoProfile'))
                        <li>
                            <a class="nf-settings-link" href="{{ url()->route('instance.profiles', ['profileType' => 'houses']) }}">
                                {{ trans('instances.menu.houses') }}
                            </a>
                        </li>

                        <li>
                            <a class="nf-settings-link" href="{{ url()->route('instance.profiles', ['profileType' => 'projects']) }}">
                                {{ trans('instances.menu.projects') }}
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.profiles', ['profileType' => 'communities'])) ? 'active' : '' }}" href="{{ url()->route('instance.profiles', ['profileType' => 'communities']) }}">
                            {{ trans('instances.menu.communities') }}
                        </a>
                    </li>
                    <li class="sep"></li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.profiles', ['profileType' => 'users'])) ? 'active' : '' }}" href="{{ url()->route('instance.profiles', ['profileType' => 'users']) }}">
                            {{ trans('instances.menu.users') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.rights')) ? 'active' : '' }}" href="{{ url()->route('instance.rights') }}">
                            {{ trans('instances.menu.rights') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.usersdata')) ? 'active' : '' }}" href="{{ url()->route('instance.usersdata') }}">
                            {{ trans('instances.menu.usersdata') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.auto.subscribe')) ? 'active' : '' }}" href="{{ url()->route('instance.auto.subscribe') }}">
                            {{ trans('instances.menu.autoSubscribe') }}
                        </a>
                    </li>
                    <li class="sep sep-half"></li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.create')) ? 'active' : '' }}" href="{{ url()->route('instance.create') }}">
                            {{ trans('instances.menu.create') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.invite')) ? 'active' : '' }}" href="{{ url()->route('instance.invite') }}">
                            {{ trans('instances.menu.invite') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.visitors')) ? 'active' : '' }}" href="{{ url()->route('instance.visitors') }}">
                            {{ trans('instances.menu.visitors') }}
                        </a>
                    </li>
                    {{--<li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.groups')) ? 'active' : '' }}" href="{{ url()->route('instance.groups') }}">
                            {{ trans('instances.menu.groups') }}
                        </a>
                    </li>--}}
                    <li class="sep"></li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.stats')) ? 'active' : '' }}" href="{{ url()->route('instance.stats') }}">
                            {{ trans('instances.menu.stats') }}
                        </a>
                    </li>
                    <li class="sep"></li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('instance.subscription')) ? 'active' : '' }}" href="{{ url()->route('instance.subscription') }}">
                            {{ trans('instances.menu.subscription') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nf-settings-content">
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop
@yield('scripts')
