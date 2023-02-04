@extends('layouts.page')

@section('title')
    {{ $profile->getNameDisplay() }} • {{ $globalInstanceName }}
@stop

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/css/media-list.css') }}">
@stop

@section('content')

    @include('page.profile-header', ['fromMediaExplorer' => true])

    <div class="main-container">
        <div id="nav_skipped" class="main-scroller">
            @if($confidentiality == 0 && $publicFolders == null)
                <div class="panel panel-default">
                    <div class="panel-body">
                        {{ trans('netframe.privateProfile.'.$profile->getType()) }}
                    </div>
                </div>
            @else
                <section class="documents" id="fileXplorer">
                    <div class="documents-breadcrumbs">
                        <div class="breadcrumbs">
                            <a href="{{ url()->route('medias_explorer-general') }}" title="{{ trans('xplorer.title') }}">
                                <span class="svgicon">
                                    @include('macros.svg-icons.doc-home')
                                </span>
                            </a>

                            <span class="breadcrumbs-item">
                               <span class="svgicon icon-arrowdown">
                                    @include('macros.svg-icons.arrow-down')
                                </span>
                                <span>
                                    <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}">
                                        {{ $profile->getNameDisplay() }}
                                    </a>
                                </span>
                            </span>
                            @if($refFolder != null)
                                @foreach($refFolder->getParentsTree(false, true) as $folder)
                                    <span class="breadcrumbs-item">
                                        <span class="svgicon icon-arrowdown">
                                            @include('macros.svg-icons.arrow-down')
                                        </span>
                                        <span>
                                            <a href="{{ $folder->getUrl() }}">
                                                @if($folder->personnal_folder)
                                                        @if(class_basename($profile) == 'User')
                                                            {{ $folder->profile->getNameDisplay() }}
                                                        @else
                                                            {{ $folder->user->getNameDisplay() }}
                                                        @endif
                                                @elseif($folder->default_folder == 0)
                                                    {{ $folder->name }}
                                                @else
                                                    {{ trans('xplorer.defaultFolders.'.$folder->name) }}
                                                @endif
                                            </a>
                                        </span>
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="xplorer-main-view {{ (session('userXplorerView') == 'list') ? 'netframe-list-wrapper' : 'netframe-grid-wrapper' }}" data-profile-type="{{ $profileType }}" data-profile-id="{{ $profileId }}" data-folder-id="{{ $idFolder }}">
                        <ul class="netframe-list file-display">
                            @if($channelFolders)
                                <li>
                                    <div class="item">
                                        <a href="{{ url()->route('medias_explorer', ['profileType' => $profileType, 'profileId' => $profileId, 'folder' => 'channels']) }}" class="nf-invisiblink"></a>
                                        <div class="item-icon">
                                            <span class="svgicon">
                                                @include('macros.svg-icons.channel')
                                            </span>
                                        </div>
                                        <div class="document-infos">
                                            <h4 class="document-title">
                                                {{ trans('xplorer.defaultFolders.__channels_medias') }}
                                            </h4>
                                            <p class="document-date">
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @foreach($folders as $folder)
                                @if(class_basename($folder) == 'MediasFolder')
                                    @include('media.xplorer.folder')
                                @elseif(class_basename($folder) == 'Channel')
                                    <li>
                                        <div class="item">
                                            <a href="{{ url()->route('medias_explorer', ['profileType' => 'channel', 'profileId' => $folder->id]) }}" class="nf-invisiblink"></a>
                                            <div class="item-icon">
                                                <span class="svgicon">
                                                    @include('macros.svg-icons.channel')
                                                </span>
                                            </div>
                                            <div class="document-infos">
                                                <h4 class="document-title">
                                                    {{ $folder->name }}
                                                </h4>
                                                <p class="document-date">
                                                    {{ \App\Helpers\DateHelper::xplorerDate($folder->created_at, $folder->updated_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach

                            @if(isset($driveFolders))
                                @foreach ($driveFolders as $folder)
                                    @include('media.xplorer.drive.folder')
                                @endforeach
                            @endif

                            @foreach ($medias as $media)
                                @include('media.xplorer.file')
                            @endforeach

                            @if(isset($driveFiles))
                                @foreach ($driveFiles as $media)
                                    @include('media.xplorer.drive.file')
                                @endforeach
                            @endif
                        </ul>
                    </div>

                </section>
            @endif
        </div>
    </div>



@stop

@section('sidebar')
    @if(class_basename($profile) == 'Channel')
        @include('components.sidebar-channels', ['channel' => $profile])
    @elseif(class_basename($profile) == 'User')
        @include('components.sidebar-user')
    @else
        @include('components.sidebar')
    @endif
@stop

@section('javascripts')
@parent
    @if(isset($drive))
        @include('media.drive')
    @endif

    <script src="{{ asset('assets/vendor/infinite-scroll/jquery.infinitescroll.min.js') }}"></script>
    <script>
        fileXplorer = new FileXplorer({
            $wrapper: $('#fileXplorer'),
            $profileId: {{ $profileId }},
            $profileType: '{{ $profileType }}',
            $idFolder: '{{ $idFolder }}',
        });

        videojs.options.flash.swf = "{{ asset('packages/netframe/media/vendor/videojs/video-js.swf') }}";

        $(document).ready(function () {
            //Show list of drive folders
            $('#chooseFolder').modal('show');
            $('#chooseFolder li').css('opacity', '0.7');
            $('#chooseFolder li').click(function(event){
                $('.folderName').val(this.dataset.name);
                $('.folderId').val(this.dataset.id);
                $('.folder').prop('disabled', false);
                $('#chooseFolder li').css('opacity', '0.7');
                $(this).css('opacity', '1.0');
            });
            $('.all').click(function(event){
                $('#id').val('0');
                return true;
            });

            var $modal = $('#viewMediaModal');
            var baseUrl = '{{ url()->to('/') }}';

            audiojs.events.ready(function() {
                audiojs.createAll();
            });

            new PlayMediaModal({
                $modal: $modal,
                $modalTitle: $modal.find('.modal-title'),
                $modalContent: $modal.find('.modal-carousel .carousel-item'),
                $media: $('.viewMedia'),
                $directoryMode: true,
                baseUrl: baseUrl
            });
        })
    </script>
@stop