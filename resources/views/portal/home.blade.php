@extends('layouts.page')
@section('title')
    {{ $globalInstanceName }} • {{ trans('netframe.welcome') }}
@stop

@section('content')
    <div class="main-header">
        <div class="main-header-infos">
            <div class="svgicon">
                👋
            </div>
            <h2 class="main-header-title">{{ trans('netframe.welcome') }}, {{ $dataUser->getNameDisplay() }}</h2>
        </div>
    </div>

    <div class="main-container no-side">
        <div id="nav_skipped" class="main-scroller">
            <section class="documents">
                <div class="xplorer-main-view netframe-grid-wrapper">
                    <ul class="netframe-list file-display">
                        <li>
                            <div class="item">
                                <a href="{{ url()->route('medias_explorer-general') }}" class="nf-invisiblink"></a>
                                <div class="item-icon">
                                    <span class="svgicon">
                                        @include('macros.svg-icons.doc')
                                    </span>
                                </div>
                                <div class="document-infos">
                                    <h4 class="document-title">
                                        Drive
                                    </h4>
                                    <p class="document-date">
                                    </p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item">
                                <a href="https://softinst65443.host.vifib.net/group/public/" target="_blank" class="nf-invisiblink"></a>
                                <div class="item-icon">
                                    <span class="svgicon">
                                        @include('macros.svg-icons.visio')
                                    </span>
                                </div>
                                <div class="document-infos">
                                    <h4 class="document-title">
                                        Visio
                                    </h4>
                                    <p class="document-date">
                                    </p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item">
                                <a href="" class="nf-invisiblink"></a>
                                <div class="item-icon">
                                    <span class="svgicon">
                                        @include('macros.svg-icons.talk')
                                    </span>
                                </div>
                                <div class="document-infos">
                                    <h4 class="document-title">
                                        Chat
                                    </h4>
                                    <p class="document-date">
                                    </p>
                                </div>
                            </div>
                        </li>
                        @if($activeOffice)
                            <li>
                                <div class="item">
                                    <a href="" class="nf-invisiblink"></a>
                                    <div class="item-icon">
                                        <span class="svgicon">
                                            @include('macros.svg-icons.notes')
                                        </span>
                                    </div>
                                    <div class="document-infos">
                                        <h4 class="document-title">
                                            Office
                                        </h4>
                                        <p class="document-date">
                                        </p>
                                    </div>
                                    <ul class="nf-actions">
                                        <li class="nf-action">
                                            <a href="#" class="nf-btn btn-ico btn-submenu">
                                                <span class="svgicon btn-img">
                                                    @include('macros.svg-icons.menu')
                                                </span>
                                            </a>
                                            <div class="submenu-container submenu-right">
                                                <ul class="submenu">
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
                                                            <a href="{{ url()->route('office.create', ['documentType' => 'spreadsheet', 'profileType' => 'user', 'profileId' => auth('web')->user()->id]) }}"
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
                                                            <a href="{{ url()->route('office.create', ['documentType' => 'presentation', 'profileType' => 'user', 'profileId' => auth('web')->user()->id]) }}"
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
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        <li>
                            <div class="item">
                                <a href="{{ url()->route('user.timeline') }}" class="nf-invisiblink"></a>
                                <div class="item-icon">
                                    <span class="svgicon">
                                        @include('macros.svg-icons.community')
                                    </span>
                                </div>
                                <div class="document-infos">
                                    <h4 class="document-title">
                                        Workplace
                                    </h4>
                                    <p class="document-date">
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <div class="notifications-content">
                <ul class="nf-list-settings" id="notifications-results">
                    @if(0 === count($results))
                        <li class="p-3">
                            {{ trans('notifications.no_matching_results') }}
                        </li>
                    @endif

                    @include('notifications.results-details')
                </ul>
            </div>
        </div>
    </div>


    @if((session()->has('justCreated') && !$need_local_consent))
        @include('welcome.boarding-modals')
    @endif
@stop
