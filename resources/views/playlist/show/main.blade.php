@extends('layouts.master')

@section('content')

    @if($noplaylist == 1)
        <div class="col-md-6 offset-md-3">
            <h1>{{ trans('playlist.playlist') }} </h1>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>{{ trans('playlist.no_playlist') }} </p>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-6 offset-md-3">
            <div class="row">
                <div class="col-md-12">
                    @if($playlist->users_id == auth()->guard('web')->user()->id)
                    <div class="col-md-12 text-right">
                        <a href="{{ url()->route('playlist_add') }}" class="btn btn-border-default" data-toggle="modal" data-target="#modal-ajax">
                            <span class="icon ticon-plus"></span>
                            {{ trans('netframe.new') }}
                        </a>

                        {{--
                        @if (count($playlistItems) > 0)
                            <button type="button" class="btn btn-border-default" data-toggle="modal" data-target="#playerModal">
                                <span class="icon ticon-simple-play"></span>
                                {{ trans('netframe.play') }}
                            </button>
                        @endif

                        @if ($playlist->instant_playlist == 0)
                            <a href="{{ url()->to('netframe/form-publish-playlist', [$playlist->id]) }}" class="btn btn-border-default" data-toggle="modal" data-target="#modal-ajax">
                                <span class="icon ticon-share"></span>
                                {{ trans('netframe.share') }}
                            </a>
                        @endif
                        --}}
                    </div>
                    @endif

                    <h1>
                        @if($playlist->instant_playlist == 1)
                            {{ trans('playlist.playlist') }}
                        @else
                            {{ $playlist->name }}

                        @endif
                    </h1>
                    @if($playlist->description)
                        <div class="well well-sm">
                            {{ $playlist->description }}
                        </div>
                    @endif

                    @if($playlist->instant_playlist == 1 && $playlist->users_id == auth()->guard('web')->user()->id)
                        <div class="well well-sm">
                            {{ trans('playlist.playlistIntro') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="well well-sm">
                <div data-toggle="buttons">
                    <span class="text-muted">{{ trans('playlist.filter') }} : </span>
                    <label class="btn btn-media btn-sm">
                        <span class="glyphicon glyphicon-camera"></span>
                        {{ Form::radio('filter_media_type', '1', $mediaTypeFilter === 'videos') }}
                        {{ trans('playlist.videos') }}
                    </label>

                    <label class="btn btn-media btn-sm">
                        <span class="glyphicon glyphicon-picture"></span>
                        {{ Form::radio('filter_media_type', '0', $mediaTypeFilter === 'images') }}
                        {{ trans('playlist.images') }}
                    </label>

                    <label class="btn btn-media btn-sm">
                        <span class="glyphicon glyphicon-headphones"></span>
                        {{ Form::radio('filter_media_type', '2', $mediaTypeFilter === 'audios') }}
                        {{ trans('playlist.audios') }}
                    </label>

                    <label class="btn btn-media btn-sm">
                        {{ Form::radio('filter_media_type', 'all', $mediaTypeFilter === 'all') }}
                        {{ trans('playlist.all') }}
                    </label>
                </div>
            </div>

            @if (count($playlistItems) > 0)
                @include('playlist.show.player-modal', array('playlistItems' => $playlistItems))
            @endif

            {!! \App\Helpers\ActionMessageHelper::show() !!}

            @if (count($playlistItems) === 0)
            <div class="alert alert-info" role="alert">
                {{ trans('playlist.no_matching_item') }}
            </div>
            @endif

            @foreach($playlistItems as $playlistItem)
                @include('playlist.show.item', array('playlistItem' => $playlistItem, 'otherPlaylists' => $otherPlaylists))
            @endforeach

            @if($playlist->users_id == auth()->guard('web')->user()->id && count($otherPlaylists) > 0)
            <div class="row-fluid">
                <h3 class="text-center otherPlaylistTitle">{{ trans('playlist.other_playlists') }}</h3>

                @foreach($otherPlaylists as $otherPlaylist)
                <div class="panel panel-default" id="playlist-{{ $otherPlaylist->id }}">
                    <div class="panel-body">
                        <div class="col-xs-6">
                            <a href="{{ url()->route('playlist_show', array('id' => $otherPlaylist->id)) }}">
                                {{ $otherPlaylist->name }}
                            </a>
                        </div>

                        <div class="col-xs-6 text-right">
                            {{--
                            <p class="text-muted otherPlaylistDate hidden-xs">
                                {{ date('m/d/Y H:i', strtotime($otherPlaylist->created_at)) }}
                            </p>
                            --}}

                            <a href="{{ url()->route('playlist_delete', array('id' => $otherPlaylist->id)) }}" class="btn btn-danger btn-sm fn-confirm-delete fn-ajax-delete" data-txtconfirm="{{ trans('netframe.confirmDel') }}">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>

    @endif
@stop

@section('javascripts')
@parent
    @if($noplaylist == 0)
<script>
    var playerModal = '';
    $(document).ready(function () {

        // Submit form on filter changes
        $('[name="filter_media_type"]').on('change', function () {
            mediaType = $(this).val();
            $('#active_media_filter').val(mediaType);

            mediaTypes = mediaType;
            /*
            playerModal = PlayerModal({
                $modal: $('#playerList'),
                $backwardButton: $('#backwardButton'),
                $forwardButton: $('#forwardButton'),
                $mediaTitle: $('#playerMediaTitle'),
                $playerWrapper: $('#mediaPlayerWrapper'),
                $mediaTypes: mediaType,
                baseUrl: '{{ url()->to('/') }}'
            });
            */
            displayItems(mediaType);


        });

        function displayItems(mediaType){
            $(document).find('.playlistItem').each(function(){
                if(mediaType == 'all'){
                    $(this).fadeIn('slow');
                }
                else if($(this).data('media-type') != mediaType){
                    $(this).fadeOut('slow');
                }
                else{
                    $(this).fadeIn('slow');
                }
            });
        }

        // Open the create playlist modal
        var hash = document.location.hash;

        if ('#createModal' === hash) {
            $('#createPlaylistModal').modal('show');
        }

        //move item to other playlists
        $('.addToPlaylist').click(function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            $.get(url)
                .success(function(data){
                    eltarget = data.targetId;
                    $(eltarget).fadeOut('slow', function() {
                        $(this).remove();
                    });
                });
        });
    });
</script>

    @yield('playlist.javascripts')
    @endif
@stop
