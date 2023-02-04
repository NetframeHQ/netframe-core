@extends('layouts.page')

@section('title')
    {{ $channel->getNameDisplay() }} • {{ $globalInstanceName }}
@stop

@section('favicon')
    {{ $channel->profileImageSrc() }}
@endsection

{{-- PAGE DISCUSSION GROUPE & SINGLE --}}

@section('content')
	<div class="main-header">
        <div class="main-header-infos">
            {{--<span class="svgicon back-button sidebar-toggle">@include('macros.svg-icons.send')</span>--}}
            <span class="svgicon icon-talktalk">
                @include('macros.svg-icons.channel_big')
            </span>
            <div class="main-header-title">
                @if($channel->confidentiality == 0)
                    <span class="private svgicon" title="{{ trans('messages.private') }}">
                        @include('macros.svg-icons.private')
                    </span>
                @endif
                <h2>
                    <a 
                        href="{{$channel->getUrl()}}" 
                        title="{{ $channel->getNameDisplay() }}"
                    >
                        {{ $channel->getNameDisplay() }}
                    </a>
                </h2>
            </div>
            
            @if($channel->personnal == 1 && !$channel->getUserStatus())
                <div class="main-header-subtitle">
                    <p>
                        {{ trans('channels.lastConnect') }} : {{ \App\Helpers\DateHelper::feedDate($channel->lastConnnect()) }}
                    </p>
                </div>
            @elseif ($channel->id > 0)
                <div class="main-header-subtitle">
                    <p>
                        {!! \App\Helpers\StringHelper::collapsePostText($channel->description, 200) !!}
                    </p>
                </div>
            @endif
        </div>
        {{-- CUSTOM LINKS PAGE CHANNELS/USERS --}}
        <ul class="nf-actions">
            @if($channel->personnal == 0)
                <li class="nf-action">
                    <div class="nf-lbl" title="{{ $channel->nbUsers() }} {{ trans_choice('page.members', $channel->users()->count()) }}">
                        <span class="svgicon lbl-img">
                            @include('macros.svg-icons.members-xs')
                        </span>
                        <span class="lbl-txt">
                            {{ $channel->nbUsers() }}
                        </span>
                    </div>
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
                        
                        @if($channel->personnal == 0)
                            @if($channel->profile->getType() != \App\Profile::TYPE_USER)
                                {{-- CUSTOM LINK ASSOCIATED PAGES --}}
                                <li class="submenu-linked">
                                    <a class="nf-btn" href="{{ $channel->profile->getUrl() }}">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.back')
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
                            {{-- CUSTOM LINK DOCUMENTS USER --}}
                            <li>
                                <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'channel', 'profileId' => $channel->id]) }}">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.doc')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.documents') }}
                                    </span>
                                </a>
                            </li>
                            {{-- CUSTOM LINK SETTINGS CHANNEL --}}
                            @if(isset($channel->pivot) && $channel->pivot->roles_id <= 2)
                                <li>
                                    <a class="nf-btn" href="{{ url()->route('channel.edit', ['id' => $channel->id]) }}">
                                        <span class="btn-img svgicon">
                                            @include('macros.svg-icons.settings')
                                        </span>
                                        <span class="btn-txt">
                                            {{ trans('netframe.parameters') }}
                                        </span>
                                    </a>
                                </li>
                            @endif
                            {{-- CUSTOM LINK LEAVE CHANNEL --}}
                            {{-- <li>
                                <a class="nf-btn fn-remove-join" data-confirm="{{ trans('members.quit.channel') }}" data-tl-join="{{ json_encode(['profile_id' => $channel->id, 'profile_type' => 'channel', 'users_id' => auth()->guard('web')->user()->id]) }}">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.leave')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('channels.dropdown.quit') }}
                                    </span>
                                </a>
                            </li>--}}
                        @else
                            {{-- CUSTOM LINK PROFIL USER --}}
                            <li>
                                <a class="nf-btn" href="{{ $channel->otherUser()->getUrl() }}">
                                    <span class="btn-img svgicon">
                                        @include('macros.svg-icons.user')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('netframe.viewProfile') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>
            <li class="nf-action">
                <a href="#" class="content-sidebar-toggle nf-btn btn-ico">
                    <span class="btn-img svgicon fn-close">
                        @include('macros.svg-icons.sidebar-close2')
                    </span>
                    <span class="btn-txt fn-close">
                        {{ trans('netframe.close_sidebar') }}
                    </span>
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.sidebar-open')
                    </span>
                    <span class="btn-txt">
                        {{ trans('netframe.open_sidebar') }}
                    </span>
                </a>
            </li>

            
        </ul>
    </div>
    <div id="app"></div>
@stop

@section('sidebar')
    @if($channel->personnal == 0)
        @include('components.sidebar-channels')
    @else
        @include('components.sidebar-user', ['profile' => $channel->otherUser()])
    @endif
@stop

@section('javascripts')
@parent
<script>
window.userId = {{auth()->guard('web')->user()->id}};
window.channelId = {{$channel->id}};
</script>
{{ HTML::script('js/channel.js') }}
@endsection
