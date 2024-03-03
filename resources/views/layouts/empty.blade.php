<!doctype html>
<html lang="{{ \Lang::getLocale() }}" class="auto-scroll">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', "netframe")</title>

    @include('layouts.partials.languages-alternates')

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, maximum-scale=12.0, minimum-scale=.25">
    <meta name="description" content="@yield('meta-description', trans('netframe.globalDescription'))" />

    @include('layouts.partials.scripts-header')
</head>
<body class="auto-scroll">
    <div class="col-12 col-lg-8 offset-lg-2 pb-4 pt-4">
        <div class="panel default-panel mb-5">
            <div class="panel-heading d-flex flex-column">
                <div class="d-flex justify-content-end w-100">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="https://www.netframe.co">
                                netfame.co
                            </a>
                        </li>
                        <li class="list-inline-item"> - </li>
                        <li class="list-inline-item">
                            <a href="https://work.netframe.co/boarding">
                                {{ trans('links.boarding') }}
                            </a>
                        </li>
                        <li class="list-inline-item"> - </li>
                        <li class="list-inline-item">
                            <a href="https://work.netframe.co/login">
                                {{ trans('links.login') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="mb-5 mt-4">
                    <img src="{{ asset('assets/img/widget-logo.png') }}" class="img-fluid center-block menu-logo-light {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
                    <img src="{{ asset('assets/img/widget-logo-dark.png') }}" class="img-fluid center-block menu-logo-dark {{ (isset($disableCssMode)) ? $disableCssMode : '' }}">
                </div>
                <h1 class="widget-title">@yield('title')</h1>
            </div>
            <div class="panel-body p-4">
                @yield('content')
            </div>
        </div>
    </div>
    @include('static.links')

    @include('layouts.partials.scripts-footer')

    @yield('javascripts')

</body>
</html>
