<!doctype html>
<html lang="{{ \Lang::getLocale() }}" class="full-height">
<head>
    <meta charset="UTF-8" />
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', "netframe")</title>

    @include('layouts.partials.languages-alternates')

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, maximum-scale=12.0, minimum-scale=.25">
    <meta name="description" content="@yield('meta-description', trans('netframe.globalDescription'))" />

    @include('layouts.partials.scripts-header', ['boardingLayout' => true])
    {{ HTML::style('css/boarding.css?v=2') }}
</head>
<body>
    <section class="onboarding">
        <header>
            <img class="onboarding__logo" src="{{ asset('assets/img/boarding/logo-white.png') }}" alt="logo netframe blanc" width="96" height="15" />
        </header>
        <main>
            @yield('content')
        </main>
        @include('boarding.partials.footer')
    </section>
    <div class="onboarding__thumb">
      <img src="{{ asset('assets/img/boarding/intro.svg') }}" alt="intro illustration" />
    </div>

    @include('layouts.partials.scripts-footer')
    @yield('javascripts')
</body>
</html>
