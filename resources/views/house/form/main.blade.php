@extends('layouts.master-header')

@section('title')
    {{ $house->id > 0 ? trans('house.h1EditHouse') : trans('house.h1AddHouse') }} • {{ $globalInstanceName }}
@stop

{{-- PAGE HOUSE (ENTITY) --}}
@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.settings_big')
            <!-- @include('macros.svg-icons.house_big') -->
        </span>
        <div class="main-header-title">
            @if($house->confidentiality == 0)
            <span class="private svgicon" title="{{ trans('messages.private') }}">
                @include('macros.svg-icons.private')
            </span>
            @endif
            <h2>
                @if($house->id != null)
                    <a href="{{$house->getUrl()}}" title="{{ $house->id > 0 ? $house->getNameDisplay() : trans('house.h1AddHouse') }}">
                        {{ $house->getNameDisplay() }}
                    </a>
                @else
                    {{ trans('house.h1AddHouse') }}
                @endif
            </h2>
        </div>
        @if($house->description != '' && $house->id != null)
            <div class="main-header-subtitle">
                <p>
                    {!! \App\Helpers\StringHelper::collapsePostText($house->description, 200) !!}
                </p>
            </div>
        @endif
    </div>

    {{-- CUSTOM LINKS PAGE HOUSE (ENTITY) --}}
    @if($house->id != null)
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
                        {{-- CUSTOM LINK ENTITE --}}
                        <li>
                            <a href="{{ $house->getUrl() }}" class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.back')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('house.backToHouse') }}
                                </span>
                            </a>
                        </li>
                        {{-- CUSTOM LINK ASSOCIATED PAGES --}}
                        {{-- On affiche les fils de discussion rattachés au profil --}}
                        @foreach($house->channels()->getResults() as $channel )
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
                        {{-- CUSTOM LINK DOCUMENT ENTITE --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'house', 'profileId' => $house->id ]) }}">
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
        <div class="nf-settings" id="">
            @if($house->id != null)
                <div class="nf-settings-sidebar">
                    <ul class="nf-settings-links">
                        <li>
                            <a
                                class="nf-settings-link {{(url()->current() == url()->route('house.edit', ['id' => $house->id])) ? 'active' : '' }}"
                                href="{{ url()->route('house.edit', ['id' => $house->id]) }}"
                            >
                                {{ trans('house.houseInformations') }}
                            </a>
                        </li>
                        <li class="sep"></li>
                        <li>
                            <a
                                class="nf-settings-link {{(url()->current() == url()->route('house_invite', ['id' => $house->id])) ? 'active' : '' }}"
                                href="{{ url()->route('house_invite', ['id' => $house->id]) }}"
                            >
                                {{ trans('members.invite') }}
                            </a>
                        </li>
                        @foreach(config('netframe.members_status') as $status=>$statusName)
                            <li>
                                <a
                                    class="nf-settings-link {{(url()->current() == url()->route('house_edit_community', ['id' => $house->id, 'status' => $status])) ? 'active' : '' }}"
                                    href="{{ url()->route('house_edit_community', ['id' => $house->id, 'status' => $status]) }}"
                                >
                                    {{ trans('members.community.'.$statusName) }}
                                </a>
                            </li>
                        @endforeach
                        <li class="sep"></li>
                        <li>
                            <a class="nf-settings-link {{(isset($statPage)) ? 'active' : '' }}"
                                href="{{ url()->route('profile.stats', ['profileType' => 'house', 'profileId' => $house->id]) }}">{{ trans('stats.title') }}</a>
                        </li>

                        <li class="sep"></li>
                        <li>
                            @if($house->id != null)
                                <a
                                    class="fn-confirm-delete-get nf-settings-link {{(url()->current() == url()->Route('profile.disable', ['profileType' => 'house', 'profileId' => $house->id, 'active' => ($house->active == 0) ? 1 : 0 ])) ? 'active' : '' }}"
                                    href="{{ url()->Route('profile.disable', ['profileType' => 'house', 'profileId' => $house->id, 'active' => ($house->active == 0) ? 1 : 0 ]) }}"
                                    data-txtconfirm="{{ ($house->active == 0) ? trans('house.activation.confirmEnable') : trans('house.activation.confirmDisable') }}"
                                >
                                    {{ ($house->active == 0) ? trans('house.activation.enable') : trans('house.activation.disable') }}
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            @endif
            <div class="nf-settings-content {{ ($house->id == 0) ? '' : ''}}">
                {!! \App\Helpers\ActionMessageHelper::show() !!}
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop
