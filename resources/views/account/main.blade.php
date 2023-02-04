@extends('layouts.master-header')

@section('title')
    {{ trans('user.account.accountTitle') }} • {{ $globalInstanceName }}
@stop

{{-- HEADER SETTINGS MON COMPTE --}}

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.settings_big')
        </span>
        <h2 class="main-header-title">{{ trans('user.account.accountTitle') }}</h2>
    </div>
    {{-- CUSTOM LINKS PAGE SETTINGS USER --}}
    <ul class="nf-actions">
        {{-- ••• MENU --}}
        <li class="nf-action nf-custom-nav">
            <a href="#" class="nf-btn btn-ico btn-submenu">
                <span class="svgicon btn-img">
                    @include('macros.svg-icons.menu')
                </span>
            </a>
            <div class="submenu-container submenu-right">
                <ul class="submenu">
                    {{-- CUSTOM LINK USER --}}
                    <li>
                        <a href="{{ $user->getUrl() }}" class="nf-btn">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.back')
                            </span>
                            <span class="btn-txt">
                                {{ trans('netframe.navUser') }}
                            </span>
                        </a>
                    </li>
                    {{-- CUSTOM LINK MY DOCUMENTS --}}
                    <li>
                        <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}">
                            <span class="svgicon btn-img">
                                @include('macros.svg-icons.doc')
                            </span>
                            <span class="btn-txt">
                                {{ trans('netframe.myDocuments') }}
                            </span>
                        </a>
                    </li>

                    {{-- CUSTOM LINK DIRECTORY USER --}}
                    <li>
                        <a class="nf-btn" href="{{ url()->route('directory.home') }}">
                            <span class="btn-img svgicon">
                                @include('macros.svg-icons.members')
                            </span>
                            <span class="btn-txt">
                                {{ trans('netframe.myFriends') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
@stop

@section('content')
    <div id="nav_skipped" class="main-scroller">

        <div class="nf-settings">
            <div class="nf-settings-sidebar">
                <ul class="nf-settings-links">
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('account.account')) ? 'active' : '' }}" href="{{ url()->route('account.account') }}">
                            {{ trans('user.menu.personalInformations') }}
                        </a>
                    </li>
                    <li class="sep sep-half"></li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('account.privacy')) ? 'active' : '' }}" href="{{ url()->route('account.privacy') }}">
                            {{ trans('user.menu.privacySettings') }}
                        </a>
                    </li>
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('account.notifications')) ? 'active' : '' }}" href="{{ url()->route('account.notifications') }}">
                            {{ trans('user.menu.notifications') }}
                        </a>
                    </li>
                    {{--
                    <li>
                        <a class="nf-settings-link {{(url()->current() == url()->route('account.calendars')) ? 'active' : '' }}" href="{{ url()->route('account.calendars') }}">
                            {{ trans('user.menu.calendars') }}
                        </a>
                    </li>
                    --}}
                </ul>
            </div>
            <div class="nf-settings-content">
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop

@section('javascripts')
    @parent
    <script>
    $(document).ready(function () {
        function matchStart (term, text) {
            if (text.toUpperCase().indexOf(term.toUpperCase()) == 0) {
              return true;
            }

            return false;
          }

          $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
            $(".fn-select-languages").select2({
              matcher: oldMatcher(matchStart)
            })
          });
    });
    </script>
@stop