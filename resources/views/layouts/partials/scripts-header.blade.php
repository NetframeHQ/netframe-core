<link rel="icon" href="@yield('favicon', asset('assets/img/favicon.svg')) ">

{{ HTML::style('assets/vendor/select2/css/select2.min.css') }}

{{ HTML::style('assets/vendor/fullcalendar/css/fullcalendar.min.css') }}
{{-- HTML::style('assets/vendor/fullcalendar/css/fullcalendar.print.min.css', ['media' => 'print']) --}}

{{ HTML::style('assets/vendor/netframe/jquery-ui.css') }}
{{ HTML::style('assets/vendor/jquery-mentions/jquery.mentions.css') }}

@if(!isset($boardingLayout))
    {{ HTML::style('css/style.css?v=' . env('ASSETS_VERSION', 5)) }}
    @if(isset($instanceThemeCss))
        {{ HTML::style($instanceThemeCss) }}
    @endif

    @if (isset($customAdditionnalCss))
        {{ HTML::style($customAdditionnalCss) }}
    @endif
@endif
{{ HTML::style('packages/netframe/media/vendor/fileupload/jquery.fileupload.css') }}
{{ HTML::style('packages/netframe/media/css/attachment-modal.css') }}
{{ HTML::style('packages/netframe/media/vendor/videojs/video-js.min.css') }}

@if(auth()->guard('web')->check())
    <script src="//{{ Request::getHttpHost() }}/ws/socket.io/socket.io.js"></script>
    <meta name="broadcast-domain" content="{{ config('app.protocol') }}://{{config('app.broadcastUrl')}}.{{ env('APP_BASE_DOMAIN', 'netframe.co') }}" />
    <meta name="collab-ws-url" content="{{ env('COLLAB_URL', '') }}" />
    <meta name="collab-ws-port" content="{{ env('COLLAB_PORT', '') }}" />
    <meta name="collab-ws-path" content="{{ env('COLLAB_PATH', '') }}" />
    <meta name="user-lang" content="{{ \App::getLocale() }}" />
@endif

@yield('stylesheets')
<!-- [if lt IE 9]>
{{ HTML::script('assets/js/html5shiv.min.js') }}
<![endif]-->
