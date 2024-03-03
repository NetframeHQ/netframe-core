@extends('layouts.master-header')

@section('title')
{{ $project->id > 0 ? trans('project.edit_project') : trans('project.create_your_project') }} • {{ $globalInstanceName }}
@stop

{{-- PAGE SETTINGS PROJECTS --}}

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('packages/netframe/media/css/select-modal.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('packages/netframe/media/vendor/videojs/video-js.min.css') }}"> --}}
    @yield('project.tab.stylesheets')
@stop

@section('content-header')
    <div class="main-header-infos">
        <span class="svgicon icon-talkgroup">
            @include('macros.svg-icons.project_big')
        </span>
        <div class="main-header-title">
            @if($project->confidentiality == 0)
                <span class="private svgicon" title="{{ trans('messages.private') }}">
                    @include('macros.svg-icons.private')
                </span>
            @endif
            <h2>
                @if($project->id != null)
                    <a href="{{$project->getUrl()}}" title="{{ $project->id > 0 ? $project->getNameDisplay() : trans('project.create_your_project') }}">
                        {{ $project->getNameDisplay() }}
                    </a>
                @else
                    {{ trans('project.create_your_project') }}
                @endif
            </h2>
        </div>
        @if($project->description != '' && $project->id != null)
            <div class="main-header-subtitle">
                <p>
                    {!! \App\Helpers\StringHelper::collapsePostText($project->description, 200) !!}
                </p>
            </div>
        @endif
    </div>
    {{-- CUSTOM LINKS PAGE SETTINGS PROJECTS --}}
    @if($project->id != null)
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
                        {{-- CUSTOM LINK PROJETS --}}
                        <li>
                            <a href="{{ $project->getUrl() }}" class="nf-btn">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.back')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('project.backToProject') }}
                                </span>
                            </a>
                        </li>
                        @foreach($project->channels()->getResults() as $channel )
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
                        {{-- CUSTOM LINK DOCUMENT PROJECTS --}}
                        <li>
                            <a class="nf-btn" href="{{ url()->route('medias_explorer', ['profileType' => 'project', 'profileId' => $project->id ]) }}">
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
            @if($project->id > 0)
                <div class="nf-settings-sidebar">
                    <ul class="nf-settings-links">
                        <li>
                            <a class="nf-settings-link {{(url()->current() == url()->route('project.edit', ['id' => $project->id])) ? 'active' : '' }}"
                                href="{{ url()->route('project.edit', ['id' => $project->id]) }}">{{ trans('project.projectInformations') }}</a>
                        </li>
                        {{--
                            <li>
                                <a class="nf-settings-link {{(url()->current() == url()->route('project_edit_bookmarks', ['id' => $project->id])) ? 'active' : '' }}"
                                    href="{{ url()->route('project_edit_bookmarks', ['id' => $project->id]) }}">{{ trans('project.bookmarks') }}</a>
                            </li>
                        --}}
                        <li class="sep"></li>
                        <li>
                            <a class="nf-settings-link {{(url()->current() == url()->route('project_invite', ['id' => $project->id])) ? 'active' : '' }}"
                                href="{{ url()->route('project_invite', ['id' => $project->id]) }}">{{ trans('members.invite') }}</a>
                        </li>
                        @foreach(config('netframe.members_status') as $status=>$statusName)
                            <li>
                                <a class="nf-settings-link {{(url()->current() == url()->route('project_edit_community', ['id' => $project->id, 'status' => $status])) ? 'active' : '' }}"
                                    href="{{ url()->route('project_edit_community', ['id' => $project->id, 'status' => $status]) }}">{{ trans('members.community.'.$statusName) }}</a>
                            </li>
                        @endforeach
                        <li class="sep"></li>
                        <li>
                            <a class="nf-settings-link {{(isset($statPage)) ? 'active' : '' }}"
                                href="{{ url()->route('profile.stats', ['profileType' => 'project', 'profileId' => $project->id]) }}">{{ trans('stats.title') }}</a>
                        </li>
                        <li class="sep"></li>
                        <li>
                            @if($project->id != null)
                                <a class="fn-confirm-delete-get nf-settings-link {{(url()->current() == url()->Route('profile.disable', ['profileType' => 'project', 'profileId' => $project->id, 'active' => ($project->active == 0) ? 1 : 0 ])) ? 'active' : '' }}"
                                    href="{{ url()->Route('profile.disable', ['profileType' => 'project', 'profileId' => $project->id, 'active' => ($project->active == 0) ? 1 : 0 ]) }}"
                                    data-txtconfirm="{{ ($project->active == 0) ? trans('project.activation.confirmEnable') : trans('project.activation.confirmDisable') }}"
                                    >
                                    {{ ($project->active == 0) ? trans('project.activation.enable') : trans('project.activation.disable') }}
                                </a>
                            @endif
                        </li>
                    </ul>
                </div>
            @endif
            <div class="nf-settings-content {{ ($project->id == 0) ? '' : ''}}">
                {!! \App\Helpers\ActionMessageHelper::show() !!}
                @yield('subcontent')
            </div>
        </div>
    </div>
@stop

@section('javascripts')
@parent
<script src="{{ asset('js/laroute.js') }}"></script>

<!-- Start Select media modal -->
<script src="{{ asset('packages/netframe/media/vendor/jquery-bootpag/jquery.bootpag.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/handlebars/handlebars.min.js') }}"></script>
<script src="{{ asset('packages/netframe/media/js/select-modal.js') }}"></script>
<!-- End Select media modal -->
</script>

@yield('project.tab.javascripts')
@stop
