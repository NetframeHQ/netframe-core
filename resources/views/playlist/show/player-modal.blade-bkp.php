<div class="modal fade" id="playerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="playerMediaTitle"></h4>
            </div>

            <div class="modal-body">
                <div id="mediaPlayerWrapper"></div>
            </div>

            <div class="modal-footer playerModalFooter">

                <a class="btn btn-default" id="backwardButton">
                    <span class="glyphicon glyphicon-backward"></span>
                </a>

                <a class="btn btn-default" id="forwardButton">
                    <span class="glyphicon glyphicon-forward"></span>
                </a>

            </div>

        </div>
    </div>
</div>


@section('playlist.javascripts')
    <script src="{{ asset('js/laroute.js') }}"></script>
    <script src="{{ asset('assets/js/playlist/player-modal.js') }}"></script>
    <script src="{{ asset('packages/netframe/media/vendor/audiojs/audio.min.js') }}"></script>
    <script src="{{ asset('packages/netframe/media/vendor/videojs/video.js') }}"></script>
    <script>videojs.options.flash.swf = "{{ asset('packages/netframe/media/vendor/videojs/video-js.swf') }}";</script>

    <script>
        $(document).ready(function () {
            PlayerModal({
                $modal: $('#playerModal'),
                $backwardButton: $('#backwardButton'),
                $forwardButton: $('#forwardButton'),
                $mediaTitle: $('#playerMediaTitle'),
                $playerWrapper: $('#mediaPlayerWrapper'),
                baseUrl: '{{ url()->to('/') }}'
            });
        });
    </script>
@stop
