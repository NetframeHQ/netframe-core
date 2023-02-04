@extends('layouts.master-header')

@section('title')
    {{ $community->id > 0 ? trans('community.h1EditCommunity') : trans('community.h1AddCommunity') }} • {{ $globalInstanceName }}
@stop

{{-- PAGE SETTINGS COMMUNITY (GROUPES) --}}

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            <!-- <@include('macros.svg-icons.community_big') -->
            @include('macros.svg-icons.settings_big')
        </span>

        <div class="main-header-title">
            @if($community->confidentiality == 0)
            <span class="private svgicon" title="{{ trans('messages.private') }}">
                @include('macros.svg-icons.private')
            </span>
            @endif
            <h2>
                @if($community->id != null)
                    <a href="{{$community->getUrl()}}" title="{{ $community->id > 0 ? $community->getNameDisplay() : trans('community.h1AddCommunity') }}">
                        {{ $community->getNameDisplay() }}
                    </a>
                @else
                    {{ trans('community.h1AddCommunity') }}
                @endif
            </h2>
        </div>

        @if($community->description != '' && $community->id > 0)
            <div class="main-header-subtitle">
                <p>
                    {!! \App\Helpers\StringHelper::collapsePostText($community->description, 200) !!}
                </p>
            </div>
        @endif
    </div>

    {{-- CUSTOM LINKS PAGE SETTINGS COMMUNITY (GROUPS) --}}
    @if($community->id != null)
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
                        {{-- CUSTOM LINK GROUPES --}}
                        <li>
                            <a href="{{ $community->getUrl() }}" class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.back')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('community.backToCommunity') }}
                                </span>
                            </a>
                        </li>
                        {{-- CUSTOM LINK ASSOCIATED PAGES --}}
                        {{-- On affiche les fils de discussion rattachés au profil --}}
                        @foreach($community->channels()->getResults() as $channel )
                            <li class="submenu-linked">
                                <a class="nf-btn" href="{{ $channel->getUrl() }}">
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.'.$channel->getType())
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.channel') }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                        {{-- CUSTOM LINK DOCUMENT GROUPES --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'community', 'profileId' => $community->id ]) }}">
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
        <div class="nf-settings">
            @if($community->id != null)
                <div class="nf-settings-sidebar">
                    <ul class="nf-settings-links">
                        <li>
                            <a class="nf-settings-link {{(url()->current() == url()->route('community.edit', ['id' => $community->id])) ? 'active' : '' }}"
                                href="{{ url()->route('community.edit', ['id' => $community->id]) }}">{{ trans('community.communityInformations') }}</a>
                        </li>
                        <li class="sep"></li>
                        <li>
                            <a class="nf-settings-link {{(url()->current() == url()->route('community_invite', ['id' => $community->id])) ? 'active' : '' }}"
                                href="{{ url()->route('community_invite', ['id' => $community->id]) }}">{{ trans('members.invite') }}</a>
                        </li>
                        @foreach(config('netframe.members_status') as $status=>$statusName)
                            <li>
                                <a class="nf-settings-link {{(url()->current() == url()->route('community_edit_community', ['id' => $community->id, 'status' => $status])) ? 'active' : '' }}"
                                    href="{{ url()->route('community_edit_community', ['id' => $community->id, 'status' => $status]) }}">{{ trans('members.community.'.$statusName) }}</a>
                            </li>
                        @endforeach
                        <li class="sep"></li>
                        <li>
                            @if($community->id != null)
                                <a class="fn-confirm-delete-get nf-settings-link {{(url()->current() == url()->Route('profile.disable', ['profileType' => 'community', 'profileId' => $community->id, 'active' => ($community->active == 0) ? 1 : 0])) ? 'active' : '' }}"
                                    href="{{ url()->Route('profile.disable', ['profileType' => 'community', 'profileId' => $community->id, 'active' => ($community->active == 0) ? 1 : 0]) }}"
                                    data-txtconfirm="{{ ($community->active == 0) ? trans('community.activation.confirmEnable') : trans('community.activation.confirmDisable') }}">
                                    {{ ($community->active == 0) ? trans('community.activation.enable') : trans('community.activation.disable') }}
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            @endif
            <div class="nf-settings-content {{ ($community->id == 0) ? '' : ''}}">
                {!! \App\Helpers\ActionMessageHelper::show() !!}
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop