@extends('layouts.master')

@section('content')
    <div class="col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-sm-8 offset-sm-2">
        <div class="row">
            <div class="col-xs-12 col-md-6 playlistTitle">
                <h2 class="widget-title">{{ trans('playlist.playlist') }} </h2>
            </div>

            <div class="col-xs-12 col-md-6 playlistButtons">
            {{--
            @if (count($playlistItems) > 0)
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#playerModal">
                    <span class="glyphicon glyphicon-play"></span>
                    {{ trans('netframe.play') }}
                </button>
            @endif
            --}}
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row text-center">
                    <a href="{{ url()->route('auth.register') }}" class="btn btn-primary white-space-normal">
                        {{ trans('playlist.create_to_save') }}
                    </a>
                </div>
            </div>
        </div>

        @if (count($playlistItems) > 0)
            @include('playlist.show.player-modal', array('playlistItems' => $playlistItems))
        @endif

        @foreach($playlistItems as $playlistItem)
            @include('playlist.show.item', array('playlistItem' => $playlistItem))
        @endforeach
    </div>
@stop

@section('javascripts')
@parent
<script>
    $(document).ready(function () {

     // Submit form on filter changes
        $('[name="filter_media_type"]').on('change', function () {
            mediaType = $(this).val();
            $('#active_media_filter').val(mediaType);

            PlayerModal({
                $modal: $('#playerList'),
                $backwardButton: $('#backwardButton'),
                $forwardButton: $('#forwardButton'),
                $mediaTitle: $('#playerMediaTitle'),
                $playerWrapper: $('#mediaPlayerWrapper'),
                $mediaTypes: mediaType,
                baseUrl: '{{ url()->to('/') }}'
            });
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

        // Submit form on filter changes
        $('[name="filter_media_type"]').on('change', function () {
            $('#playlistFilterForm').submit();
        });
   });
</script>
    @yield('playlist.javascripts')
@stop
