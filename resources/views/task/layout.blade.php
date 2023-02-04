@extends('layouts.master-header')

@section('favicon')
{{url()->route('netframe.svgicon', ['name' => 'tasks'])}}
@endsection


@section('title')
  {{ trans('task.title') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
  @parent
    <link href="https://illisite.netframe.info/packages/netframe/media/vendor/videojs/video-js.min.css" rel="stylesheet">
    <!-- Start Select media modal -->
    <link rel="stylesheet" href="https://illisite.netframe.info/packages/netframe/media/css/select-modal.css">
@stop

@section('content-header')
  <div class="main-header-infos">
    <span class="svgicon icon-talkgroup">
      @include('macros.svg-icons.tasks_big')
    </span>
    <h2 class="main-header-title">
      {{ trans('task.title') }}
    </h2>
  </div>
  <ul class="nf-actions">
    <li class="nf-action">
      <a class="nf-btn" href="{{ url()->route('task.addProject') }}" >
        <span class="svgicon btn-img">
          @include('macros.svg-icons.plus')
        </span>
        <span class="btn-txt">
          {{ trans('task.createProject') }}
        </span>
      </a>
    </li>

    {{-- CUSTOM NAV GRID/LIST --}}
    <li class="nf-action nf-custom-nav">
        <a href="#" class="nf-btn btn-ico btn-submenu">
            <span class="svgicon btn-img {{ (session('userXplorerView') == 'grid') ? 'd-none' : '' }}">
                @include('macros.svg-icons.list-line')
            </span>
            <span class="svgicon btn-img {{ (session('userXplorerView') == 'list') ? 'd-none' : '' }}">
                @include('macros.svg-icons.list-grid')
            </span>
        </a>
        <div class="submenu-container submenu-right">
            <ul class="submenu">
                <li>
                    <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'list') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-list-wrapper" data-view-slug="list">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.list-line')
                        </span>
                        <span class="btn-txt">
                            Visualiser en ligne
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'grid') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-grid-wrapper" data-view-slug="grid">
                        <span class="svgicon btn-img">
                            @include('macros.svg-icons.list-grid')
                        </span>
                        <span class="btn-txt">
                            Visualiser en grille
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nf-action">
      <a class="nf-btn btn-ico" href="{{route('task.editTemplates')}}" title="{{ trans('task.editTemplates') }}">
        <span class="btn-img svgicon">
          @include('macros.svg-icons.settings')
        </span>
      </a>
    </li>
  </ul>
@endsection
@section('content')
  @yield('subcontent')
@endsection