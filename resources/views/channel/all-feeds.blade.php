@extends('layouts.master-header')

@section('title')
    {{ trans('channels.myFeeds.title') }} • {{ $globalInstanceName }}
@stop

{{-- HEADER OF MANAGE CHANNELS --}}

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon">
            @include('macros.svg-icons.manage_big')
        </span>
        <h1 class="main-header-title">{{ trans('channels.dropdown.manage') }}</h1>
    </div>
@stop

@section('content')
    <div id="nav_skipped" class="main-scroller">

        <div class="nf-settings">
            <div class="nf-settings-content">
                <div class="nf-form">
                    <ul class="nf-list-settings">
                        @if($channels->count() > 0)
                            @foreach($channels as $channel)
                                <li class="nf-list-setting channel-{{ $channel->id }} @if($channel->active == 0) disabled @endif">
                                    <a href="{{ $channel->getUrl() }}" class="nf-invisiblink" ></a>
                                    <div class="svgicon">
                                        @include('macros.svg-icons.channel')
                                    </div>
                                    <div class="nf-list-infos">
                                        <h4 class="nf-list-title">
                                            {{ $channel->name }} 
                                        </h4>
                                        <span class="nf-list-subtitle">
                                            {{ trans('instances.profiles.createdAt') }} : {{ \App\Helpers\DateHelper::feedDate($channel->created_at) }}
                                        </span>
                                    </div>
                                    
                                    {{-- ••• MENU --}}

                                    <ul class="nf-actions">
                                        @if($channel->active ==0)
                                            <li class="nf-action">
                                                <span class="profile-status nf-lbl">
                                                    <span class="lbl-txt">
                                                        {{ trans('profiles.manage.disabled') }}
                                                    </span>
                                                </span>
                                            </li>
                                        @endif
                                        <li class="nf-action nf-custom-nav">
                                            <a href="#" class="nf-btn btn-ico btn-submenu">
                                                <span class="svgicon btn-img">
                                                    @include('macros.svg-icons.menu')
                                                </span>
                                            </a>
                                            <div class="submenu-container submenu-right">
                                                <ul class="submenu">

                                                    {{-- CUSTOM LINK ASSOCIATED PAGES --}}
                                                    @if($channel->profile->getType() != \App\Profile::TYPE_USER)
                                                        <li class="submenu-linked">
                                                            <a class="nf-btn" href="{{ $channel->profile->getUrl() }}">
                                                                <span class="btn-img svgicon">
                                                                    @include('macros.svg-icons.'.$channel->profile->getType())
                                                                </span>
                                                                <span class="btn-txt">
                                                                    {{ $channel->profile->getName() }}
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
                                                    {{-- CUSTOM LINK SETTINGS CHANNEL --}}
                                                    <li>
                                                        <a class="nf-btn" href="{{ url()->route('channel.edit', ['id' => $channel->id]) }}">
                                                            <span class="svgicon btn-img">
                                                                @include('macros.svg-icons.settings')
                                                            </span>
                                                            <span class="btn-txt">
                                                                {{ trans('netframe.myInstance') }}
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="nf-action">
                                            <a href="#" class="nf-btn btn-submenu btn-ico">
                                                <span class="btn-img svgicon">
                                                    @include('macros.svg-icons.menu')
                                                </span>
                                            </a>
                                            <div class="submenu-container submenu-right">
                                                <ul class="submenu">
                                                    <li>
                                                        <a
                                                            class="nf-btn fn-confirm-delete-get {{(url()->current() == url()->route('channels.disable', ['id' => $channel->id, 'active' => ($channel->active == 0) ? 1 : 0])) ? 'active' : '' }}"
                                                            href="{{ url()->route('channels.disable', ['id' => $channel->id, 'active' => ($channel->active == 0) ? 1 : 0]) }}"
                                                            data-txtconfirm="{{ ($channel->active == 0) ? trans('channels.edit.confirmEnable') : trans('channels.edit.confirmDisable') }}"
                                                        >
                                                            <span class="btn-txt">
                                                                {{ ($channel->active == 0) ? trans('channels.edit.enable') : trans('channels.edit.disable') }} "{{ $channel->name }}"
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop