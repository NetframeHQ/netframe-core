@extends('layouts.master-header')

@section('title')
    {{ trans('profiles.manage.title.'.$profileType) }} • {{ $globalInstanceName }}
@stop

{{-- PAGE MANAGE ENTITY/GROUP/PROJECTS --}}

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.manage_big')
        </span>
        <h2 class="main-header-title">
            {{ trans('netframe.manageYour'.ucfirst($profileType)) }}
        </h2>
    </div>
@stop

@section('content')
    <div id="nav_skipped" class="main-scroller">

        <div class="nf-settings">
            <div class="nf-settings-content">
                <div class="nf-form">
                    <ul class="nf-list-settings">
                        @foreach($profiles as $profile)
                            <li class="nf-list-setting profile-{{ $profile->id }} @if($profile->active == 0) disabled @endif">
                                <a href="{{ $profile->getUrl() }}" class="nf-invisiblink"></a>
                                @if($profile->profileImage)
                                    <span class="avatar">
                                        {!! HTML::thumbnail($profile->profileImage, '30', '30', array('class' => ''), asset('assets/img/avatar/'.$profile->getType().'.jpg')) !!}
                                    </span>
                                @else
                                    <div class="svgicon">
                                        @include('macros.svg-icons.'.$profileType)
                                    </div>
                                @endif
                                <div class="nf-list-infos">
                                    <h4 class="nf-list-title">
                                        {{ $profile->getNameDisplay() }}
                                    </h4>
                                    <span class="nf-list-subtitle">
                                        {{ trans('instances.profiles.createdAt') }} : {{ \App\Helpers\DateHelper::feedDate($profile->created_at) }}
                                    </span>
                                </div>

                                <ul class="nf-actions">
                                    @if($profile->active ==0)
                                        <li class="nf-action">
                                            <span class="profile-status nf-lbl">
                                                <span class="lbl-txt">
                                                    {{ trans('profiles.manage.disabled') }}
                                                </span>
                                            </span>
                                        </li>
                                    @endif
                                    {{-- CUSTOM LINKS MANAGE ENTITY/GROUP/PROJECTS --}}
                                    {{-- ••• MENU --}}
                                    <li class="nf-action nf-custom-nav">
                                        <a href="#" class="nf-btn btn-ico btn-submenu">
                                            <span class="svgicon btn-img">
                                                @include('macros.svg-icons.menu')
                                            </span>
                                        </a>
                                        <div class="submenu-container submenu-right">
                                            <ul class="submenu">
                                                {{-- CUSTOM LINK ASSOCIATED PAGES  --}}
                                                @foreach($profile->channels()->getResults() as $channel )
                                                    <li class="submenu-linked">
                                                        <a class="nf-btn" href="{{ $channel->getUrl() }}">
                                                            <span class="svgicon btn-img">
                                                                @include('macros.svg-icons.'.$channel->getType())
                                                            </span>
                                                            <span class="btn-txt">
                                                                {{ $channel->getNameDisplay() }}
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endforeach 

                                                {{-- CUSTOM LINK DOCUMENT CHANNEL --}}
                                                <li>
                                                    <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}">
                                                        <span class="svgicon btn-img">
                                                            @include('macros.svg-icons.doc')
                                                        </span>
                                                        <span class="btn-txt">
                                                            {{ trans('netframe.documents') }}
                                                        </span>
                                                    </a>
                                                </li>
                                                @if($profile->pivot->roles_id < 3)
                                                    {{-- CUSTOM LINK SETTINGS CHANNEL --}}
                                                    <li>
                                                        <a class="nf-btn" href="{{ url()->route($profileType.'.edit', ['id' => $profile->id])  }}">
                                                            <span class="svgicon btn-img">
                                                                @include('macros.svg-icons.settings')
                                                            </span>
                                                            <span class="btn-txt">
                                                                {{ trans('netframe.myInstance') }}
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endif
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
                                                    <a class="fn-confirm-delete-get nf-btn" 
                                                        href=""
                                                    >
                                                        <span class="btn-txt">
                                                            {{ ($profile->active == 0) ? trans('channels.edit.enable') : trans('instances.profiles.disable') }} "{{ $profile->name }}"
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
