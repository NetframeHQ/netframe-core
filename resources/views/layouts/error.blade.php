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

    @include('layouts.partials.scripts-header')
</head>
<body class="with-background">

    <div class="row">
        <div class="col-md-4 offset-md-4 column">
        <div class="card">
                <div class="card-heading text-center">
                    <a href="{{ url()->route('home') }}">
                        <img src="{{ asset('assets/img/logo-full.png') }}" class="img-fluid">
                    </a>
                <div class="card-body text-center">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>


    <!-- Javascript -->
    <script>
    var baseUrl = '{{ url()->to('/') }}';
    </script>

</body>
</html>