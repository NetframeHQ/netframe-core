@extends('layouts.master-header')

@section('title')
    {{ $channel->id > 0 ? trans('channels.edit.h1Edit') : trans('channels.edit.h1Add') }} • {{ $globalInstanceName }}
@stop

{{-- PAGE SETTINGS CHANNELS --}}

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            <!-- @include('macros.svg-icons.channel_big') -->
            @include('macros.svg-icons.settings_big')
        </span>
        <div class="main-header-title">
            @if($channel->confidentiality == 0)
                <span class="private svgicon" title="{{ trans('messages.private') }}">
                    @include('macros.svg-icons.private')
                </span>
            @endif

            <h2>
                @if($channel->id != null)
                    <a
                        href="{{$channel->getUrl()}}"
                        title="{{ trans('channels.backToChannel') }}"
                    >
                        {{ $channel->getNameDisplay() }}
                    </a>
                @endif
            </h2>
        </div>
        @if($channel->description != '' && $channel->id > 0)
            <div class="main-header-subtitle">
                <p>
                    {!! \App\Helpers\StringHelper::collapsePostText($channel->description, 200) !!}
                </p>
            </div>
        @endif
    </div>
    {{-- CUSTOM LINKS PAGE SETTINGS CHANNELS --}}
    @if($channel->id > 0)
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
                        {{-- CUSTOM LINK CHANNEL --}}
                        <li class="nf-action">
                            <a href="{{ $channel->getUrl() }}" class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.back')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('channels.backToChannel') }}
                                </span>
                            </a>
                        </li>
                        {{-- CUSTOM LINK ASSOCIATED PAGES --}}
                        @if($channel->profile->getType() != 'user')
                            <li class="submenu-linked">
                                <a class="nf-btn" href="{{ $channel->profile->getUrl() }}">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.'.$channel->profile->getType())
                                    </span>
                                    <span class="btn-txt">
                                        @if($channel->profile->getType() === 'house')
                                            {{ trans('house.backToHouse') }}
                                        @elseif($channel->profile->getType() === 'community')
                                            {{ trans('community.backToCommunity') }}
                                        @elseif($channel->profile->getType() === 'project')
                                            {{ trans('project.backToProject') }}
                                        @elseif($channel->profile->getType() === 'channels')
                                            {{ trans('channels.backToChannel') }}
                                        @else
                                            {{ $channel->profile->getName() }}
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endif

                        {{-- CUSTOM LINK DOCUMENT CHANNEL --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'channel', 'profileId' => $channel->id ]) }}">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.doc')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.documents') }}
                                </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>
        </ul>
    @endif
@stop

@section('content')
    <div id="nav_skipped" class="main-scroller">
        <!--<div id="fn-project-community" class="alert hide" role="alert"></div>-->
        <div class="nf-settings">
            @if($channel->id > 0)
                <div class="nf-settings-sidebar">
                    <ul class="nf-settings-links">
                        <li>
                            <a
                                class="nf-settings-link {{(url()->current() == url()->route('channel.edit', ['id' => $channel->id])) ? 'active' : '' }}"
                                href="{{ url()->route('channel.edit', ['id' => $channel->id]) }}"
                            >
                                {{ trans('channels.edit.channelInformations') }}
                            </a>
                        </li>
                        <li class="sep"></li>
                        <li>
                            <a
                                class="nf-settings-link {{(url()->current() == url()->route('channel_invite', ['id' => $channel->id])) ? 'active' : '' }}"
                                href="{{ url()->route('channel_invite', ['id' => $channel->id]) }}"
                            >
                                {{ trans('members.invite') }}
                            </a>
                        </li>
                        @foreach(config('netframe.members_status') as $status=>$statusName)
                            <li>
                                <a
                                    class="nf-settings-link {{(url()->current() == url()->route('channel_edit_community', ['id' => $channel->id, 'status' => $status])) ? 'active' : '' }}"
                                    href="{{ url()->route('channel_edit_community', ['id' => $channel->id, 'status' => $status]) }}"
                                >
                                    {{ trans('members.community.'.$statusName) }}
                                </a>
                            </li>
                        @endforeach
                        <li class="sep"></li>
                        @if($channel->id != null)
                            <li>
                                <a
                                    class="nf-settings-link fn-confirm-delete-get {{(url()->current() == url()->route('channels.disable', ['id' => $channel->id, 'active' => ($channel->active == 0) ? 1 : 0])) ? 'active' : '' }}"
                                    href="{{ url()->route('channels.disable', ['id' => $channel->id, 'active' => ($channel->active == 0) ? 1 : 0]) }}"
                                    data-txtconfirm="{{ ($channel->active == 0) ? trans('channels.edit.confirmEnable') : trans('channels.edit.confirmDisable') }}"
                                >
                                    {{ ($channel->active == 0) ? trans('channels.edit.enable') : trans('channels.edit.disable') }}
                                </a>
                            </li>
                            <li>
                                <a
                                    href="{{ url()->route('channels.delete', ['id' => $channel->id]) }}"
                                    class="nf-settings-link fn-confirm-delete-get {{(url()->current() == url()->route('channels.delete', ['id' => $channel->id])) ? 'active' : '' }}"
                                    data-txtconfirm="{{ trans('channels.edit.confirmDelete') }}"
                                >
                                    {{ trans('channels.edit.delete') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
            <div class="nf-settings-content {{ ($channel->id == 0) ? '' : ''}}">
                {!! \App\Helpers\ActionMessageHelper::show() !!}
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop