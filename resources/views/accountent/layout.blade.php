<!doctype html>
<html lang="{{ \Lang::getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', "netframe")</title>

    @include('layouts.partials.languages-alternates')

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta-description', trans('netframe.globalDescription'))" />

    @include('accountent.layouts.scripts-header')
</head>
<body>
    <div id="container">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="container-fluid">
                <div id="content" class="row @yield('customCssContent')">
                    {{--<a href="{{ url()->route('static_faq') }}" class="help-button hidden-xs"><span class="icon ticon-guess"></span> {{ trans('links.faqShort') }}</a>--}}
                    @yield('content')
                </div>
            </div>
        </div>
    </div>


    <footer class="footer">
        @include('components.footer')
    </footer>

    @yield('modals')

    <netframe-chat></netframe-chat>

    {{ HTML::script('assets/js/jquery.min.js') }}
    @yield('javascripts')

</body>
</html>
