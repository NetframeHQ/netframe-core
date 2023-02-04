<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <title>Admin @yield('title')</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{ HTML::script('assets/js/modernizr.custom-2.8.3.js') }}

    {{ HTML::style('assets/css/bootstrap.css') }}
    {{ HTML::style('assets/css/bootstrap/bootstrap-switch.min.css') }}
    {{ HTML::style('assets/css/bootstrap/bootstrap-table.min.css') }}
    {{ HTML::style('assets/css/bootstrap/bootstrap-table-sortable.css') }}
    {{ HTML::style('assets/css/animate.css') }}
    {{ HTML::style('assets/css/admin/admin.css') }}

    {{ HTML::style('assets/vendor/select2/css/select2.min.css') }}
    @yield('stylesheets')
    <!-- [if lt IE 9]>
    {{ HTML::script('assets/js/html5shiv.min.js') }}
    <![endif]-->
</head>
<body>
@include('admin.include.modal-ajax')

    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            @include('admin.menu.navigation')
            @include('admin.menu.sidebar')
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">
                <div class="row">
                    @yield('content')
                </div>
            </div>

        </div>

    </div>

    <!-- Javascript -->
{{ HTML::script('assets/js/jquery.min.js') }}
{{ HTML::script('assets/js/bootstrap.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-growl.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-switch.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-table.min.js') }}
{{ HTML::script('assets/js/plugins/bootstrap-table-sortable.js') }}
{{ HTML::script('assets/js/admin/jquery.netframe.js') }}
{{ HTML::script('assets/vendor/select2/js/select2.full.min.js') }}
{{ HTML::script('assets/js/admin/app.js') }}
{{ HTML::script('js/laroute.js') }}
@yield('javascript')
</body>
</html>