{{-- HTML::favicon() --}}

{{ HTML::script('assets/js/modernizr.custom-2.8.3.js') }}

{{ HTML::style('assets/css/bootstrap.css') }}
{{ HTML::style('assets/css/animate.css') }}
{{ HTML::style('assets/css/socicon.css') }}
{{ HTML::style('assets/css/bootstrap-tour.css') }}
{{ HTML::style('assets/vendor/flexslider/flexslider.css') }}
{{ HTML::style('assets/vendor/select2/css/select2.min.css') }}
{{ HTML::style('assets/vendor/bootstrap-switch/css/bootstrap-switch.min.css') }}
{{ HTML::style('assets/vendor/fullcalendar/css/fullcalendar.min.css') }}
{{-- HTML::style('assets/vendor/fullcalendar/css/fullcalendar.print.min.css', ['media' => 'print']) --}}

@if(isset($customCss))
    {{ HTML::style($customCss) }}
@else
    {{ HTML::style('assets/css/netframe.css?v=3.6') }}
@endif
{{ HTML::style('packages/netframe/media/vendor/fileupload/jquery.fileupload.css') }}
{{ HTML::style('packages/netframe/media/css/attachment-modal.css') }}
{{ HTML::style('packages/netframe/media/vendor/videojs/video-js.min.css') }}
{{ HTML::style('packages/netframe/media/vendor/videojs/videojs.watermark.css') }}

@if(auth()->guard('web')->check())
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
@endif

@yield('stylesheets')
<!-- [if lt IE 9]>
{{ HTML::script('assets/js/html5shiv.min.js') }}
<![endif]-->