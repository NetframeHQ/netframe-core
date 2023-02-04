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

    @include('layouts.partials.scripts-header')
</head>
<body @if(isset($customBackground)) {!! $customBackground !!} @endif>

    @yield('content')

    @yield('javascripts')

</body>
</html>
