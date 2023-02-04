<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <title>Admin @yield('title')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{ HTML::script('assets/js/modernizr.custom-2.8.3.js') }}

    {{ HTML::style('assets/css/bootstrap.css') }}
    {{ HTML::style('assets/css/animate.css') }}

    @yield('stylesheets')
    <!-- [if lt IE 9]>
    {{ HTML::script('assets/js/html5shiv.min.js') }}
    <![endif]-->
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            @yield('content')
        </div>
    </div>

    <!-- Javascript -->
{{ HTML::script('assets/js/jquery.min.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-growl.min.js') }}
{{ HTML::script('assets/js/admin/app.js') }}
{{ HTML::script('js/laroute.js') }}
@yield('javascript')
</body>
</html>