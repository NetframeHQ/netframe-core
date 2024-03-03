<!doctype html>
<html lang="{{ \Lang::getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', "netframe")</title>

    @include('layouts.partials.languages-alternates')

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, maximum-scale=12.0, minimum-scale=.25">
    <meta name="description" content="@yield('meta-description', trans('netframe.globalDescription'))" />

    <meta property="og:url" content="{{ Request::Url() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('ogtitle', 'netframe')">
    <meta property="og:description" content="@yield('ogdescription', 'netframe' )">
    <meta property="og:image" content="@yield('ogimage', env('APP_URL') . '/assets/img/logo-full.png')">

    @include('layouts.partials.scripts-header')
</head>
<body class="{{ $sidebarState }}">

    <div id="wrapper" class="wrapper">
        @include('components.left-sidebar')
        <div id="content" class="content">
            @include('components.navigation')
            <div class="main-header">
                @yield('content-header')
            </div>
            <div class="main-container no-side">
                @yield('content')
            </div>
        </div>
    </div>

    <footer class="footer">
        @include('components.footer')
    </footer>

    @yield('modals')
    @include('netframe.modal-ajax')

    @include('layouts.partials.scripts-footer')
    @yield('javascripts')

</body>
</html>
