@extends('layouts.page')

@section('favicon')
  {{url()->route('netframe.svgicon', ['name' => 'doc'])}}
@endsection

@section('title')
    {{ trans('xplorer.title') }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/css/media-list.css') }}">
@stop

@section('content')
    <div class="main-header">
        <div class="main-header-infos">
            <span class="svgicon icon-talkgroup">
                @include('macros.svg-icons.doc_big')
            </span>
            <h2 class="main-header-title">{{ trans('xplorer.title') }}</h2>
        </div>
        <ul class="nf-actions">

            {{-- CREATE/UPLOAD FOLDER/FILE --}}
            <li class="nf-action">
                <a href="#" class="nf-btn btn-ico btn-submenu">
                    <span class="svgicon btn-img">
                        @include('macros.svg-icons.plus')
                    </span>
                </a>

                <div class="submenu-container submenu-right">
                    <ul class="submenu">

                    @if (\App\Instance::find(session('instanceId'))->hasApplication('office'))
                        <li>
                            <a href="{{ url()->route('office.create', ['documentType' => 'document', 'profileType' => 'user', 'profileId' => auth('web')->user()->id]) }}"
                            title="{{ trans('xplorer.plusMenu.addDoc') }}"
                            class="fn-add-file nf-btn"
                            data-toggle="modal"
                            data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-text">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-text">
                                    {{ trans('xplorer.plusMenu.addDoc') }}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ url()->route('office.create', ['documentType' => 'spreadsheet', 'profileType' => 'user', 'profileId' => auth('web')->user()->id]) }}"
                                title="{{ trans('xplorer.plusMenu.addXls') }}"
                                class="fn-add-file nf-btn"
                                data-toggle="modal"
                                data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-sheet">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-sheet">
                                    {{ trans('xplorer.plusMenu.addXls') }}
                                </span>
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ url()->route('office.create', ['documentType' => 'presentation', 'profileType' => 'user', 'profileId' => auth('web')->user()->id]) }}"
                                title="{{ trans('xplorer.file.add.title') }}"
                                class="fn-add-file nf-btn"
                                data-toggle="modal"
                                data-target="#modal-ajax"
                            >
                                <span class="svgicon btn-img add-office-slide">
                                    @include('macros.svg-icons.add-file')
                                </span>
                                <span class="btn-txt add-office-slide">
                                    {{ trans('xplorer.plusMenu.addPpt') }}
                                </span>
                            </a>
                        </li>
                        @endif

                        <li>
                            {{--href="{{ url()->route('xplorer_add_file', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder, 'driveFolder' => $driveFolder]) }}"--}}
                            <a href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}"
                                class="nf-btn"
                                title="{{ trans('xplorer.plusMenu.importFile') }}"
                            >
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.export')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('xplorer.plusMenu.importFile') }}
                                </span>
                            </a>
                        </li>

                        @if(!isset($driveFolders))
                            <li>
                                {{--href="{{ url()->route('xplorer_import_folder', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder]) }}"--}}
                                <a href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}"
                                    class="nf-btn"
                                    title="{{ trans('xplorer.plusMenu.linkDrive') }}"
                                >
                                    <span class="svgicon btn-img">
                                        @include('macros.svg-icons.import')
                                    </span>
                                    <span class="btn-txt">
                                        {{ trans('xplorer.plusMenu.linkDrive') }}
                                    </span>
                                </a>
                            </li>
                        @endif

                        <li class="sep"><i></i></li>

                        <li>
                            @if(isset($driveFolder))
                                {{--href="{{ url()->route('xplorer_edit_folder', ['profileType' => $profileType, 'profileId' => $profileId, 'idFolder' => $idFolder, 'driveFolder' => $driveFolder]) }}"--}}
                                <a href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}"
                                    class="fn-add-folder nf-btn"
                                    title="{{ trans('xplorer.plusMenu.createFolder') }}">
                            @else
                                {{--href="{{ url()->route('xplorer_edit_folder', ['profileType' => $profileType, 'profileId' => $profileId]) }}?parent={{ $idFolder }}"--}}
                                <a href="{{ url()->route('medias_explorer', ['profileType' => 'user', 'profileId' => auth()->guard('web')->user()->id]) }}"
                                    class="fn-add-folder nf-btn"
                                    title="{{ trans('xplorer.plusMenu.createFolder') }}">
                            @endif
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.add-folder')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('xplorer.plusMenu.createFolder') }}
                                </span>
                            </a>
                        </li>


                    </ul>
                </div>
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
                                    {{ trans('netframe.layoutList') }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="fn-switch-display nf-btn {{ (session('userXplorerView') == 'grid') ? 'nf-customactive btn-nohov' : '' }}" data-view-mode="netframe-grid-wrapper" data-view-slug="grid">
                                <span class="svgicon btn-img">
                                    @include('macros.svg-icons.list-grid')
                                </span>
                                <span class="btn-txt">
                                    {{ trans('netframe.layoutGrid') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- SIDEBAR TOGGLE
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
            --}}
        </ul>

    </div>

    <div class="main-container  no-side">
        <div id="nav_skipped" class="main-scroller">
            <section class="documents" id="fileXplorer">
                {{--
                <!-- <div class="documents-breadcrumbs">
                    <div class="breadcrumbs">
                        <a href="{{ url()->route('medias_explorer-general') }}" title="{{ trans('xplorer.title') }}">
                            <span class="svgicon">
                                @include('macros.svg-icons.doc-home')
                            </span>
                        </a>
                    </div>
                </div> -->
                --}}

                <div class="xplorer-main-view {{ (session('userXplorerView') == 'list') ? 'netframe-list-wrapper' : 'netframe-grid-wrapper' }}">
                    <ul class="netframe-list file-display">
                        {{--
                        @if($activeCollab)
                            <li>
                                <div class="item">
                                    <a href="/collab" class="nf-invisiblink"></a>
                                    <div class="item-icon">
                                        <span class="svgicon">
                                            @include('macros.svg-icons.notes')
                                        </span>
                                    </div>
                                    <div class="document-infos">
                                        <h4 class="document-title">
                                            {{ trans('netframe.leftMenu.notes') }}
                                        </h4>
                                        <p class="document-date">
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endif
                        --}}
                        @foreach($folders as $folder)
                            <li>
                                <div class="item">
                                    <a href="{{ url()->route('medias_explorer', ['profileType' => $folder['type'], 'profileId' => $folder['profile']->id]) }}" class="nf-invisiblink"></a>
                                    <div class="item-icon">
                                        <span class="svgicon">
                                            @if($folder['type'] == 'user')
                                                @include('macros.svg-icons.doc')
                                            @else
                                                @include('macros.svg-icons.'.$folder['type'])
                                            @endif
                                        </span>
                                    </div>
                                    <div class="document-infos">
                                        <h4 class="document-title">
                                            {{ $folder['name'] }}
                                        </h4>
                                        <p class="document-date">
                                            {{ \App\Helpers\DateHelper::xplorerDate($folder['profile']->created_at) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </section>
        </div>
    </div>

@stop

@section('sidebar')
    @include('components.sidebar-user')
@stop

@section('javascripts')
    @parent
    <script>
        fileXplorer = new FileXplorer({
            $wrapper: $('#fileXplorer'),
        });
    </script>
@stop