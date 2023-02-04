<div id="playerList">
    {{-- <h4 class="player-title" id="playerMediaTitle"></h4> --}}
    <div id="mediaPlayerWrapper"></div>

    <div class="player-controls">
        <a class="link-netframe float-left" id="backwardButton">
            <span class="icon ticon-left-arrow"></span>
        </a>
        <a class="link-netframe float-right" id="forwardButton">
            <span class="icon ticon-right-arrow"></span>
        </a>

        <div class="fn-social-media"></div>


    </div>
    {{ Form::hidden('active_media_filter', 'all', ['id' => 'active_media_filter']) }}
</div>


@section('playlist.javascripts')
<script src="{{ asset('js/laroute.js') }}"></script>
<script src="{{ asset('assets/js/playlist/player-modal.js') }}"></script>
<script src="{{ asset('packages/netframe/media/vendor/audiojs/audio.min.js') }}"></script>
<script>videojs.options.flash.swf="{{ asset('packages/netframe/media/vendor/videojs/video-js.swf') }}";</script>
<script src="https://www.youtube.com/iframe_api"></script>
<script src="https://api.dmcdn.net/all.js"></script>
<script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>
<script src="https://w.soundcloud.com/player/api.js"></script>


<script>

$(document).ready(function () {
    playerModal = new PlayerModal({
            $modal: $('#playerList'),
            $backwardButton: $('#backwardButton'),
            $forwardButton: $('#forwardButton'),
            $mediaTitle: $('#playerMediaTitle'),
            $playerWrapper: $('#mediaPlayerWrapper'),
            $mediaTypes: 'all',
            baseUrl: "{{ url()->to('/') }}"
        });
    });
</script>
@stop
