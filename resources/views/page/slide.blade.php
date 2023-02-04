@include('media::player.slider-player')

@if ($profile->hasEncodedMedias())
<div id="portfolio" class="panel panel-default">
    <div class="panel-body">
        <div class="flexslider">
            <ul class="slides">
                @php
                $cptMedia = 0;
                @endphp

                @foreach ($profile->lastMedias as $media)
                    @if ($media->encoded && $media->active == 1 && ($profile->getType() == 'user' || ($media->pivot->profile_image != 1 || $media->pivot->favorite == 1)))
                        <li class="slideMedia">
                            @if (!$media->isTypeDisplay())
                                <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" data-media-position="{{ $cptMedia }}">

                            @else
                                <a href="" class="playlistItem"
                                    data-media-name="{{ $media->name }}"
                                    data-media-id="{{ $media->id }}"
                                    data-media-type="{{ $media->type }}"
                                    data-media-platform="{{ $media->platform }}"
                                    data-media-mime-type="{{ $media->mime_type }}"
                                    data-media-position="{{ $cptMedia }}"

                                    @if ($media->platform !== 'local')
                                        data-media-file-name="{{ $media->file_name }}"
                                    @endif
                                    >
                            @endif

                                {!! HTML::thumbnail($media, 115, 115, array('class' => 'img-fluid')) !!}
                                </a>

                                <div class="mediaOverlay">
                                    <a type="button" class="bookmarkIcon fn-tl-clip " data-media-id="{{ $media->id }}" data-profile-id="{{ $profile->id }}" data-profile-type="{{ $profile->getType() }}">
                                        <span class="icon ticon-clip @if (isset($instantItems[$media->id])) text-secondary @endif"></span>
                                    </a>
                                </div>
                            @php
                            $cptMedia++
                            @endphp
                        </li>
                    @endif
                @endforeach
            </ul>
         </div>
    </div>
</div>
@endif

@section('javascripts')
@parent
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="https://api.dmcdn.net/all.js"></script>
    <script src="https://f.vimeocdn.com/js/froogaloop2.min.js"></script>
    <script src="https://w.soundcloud.com/player/api.js"></script>
    <script src="{{ asset('assets/js/playlist/player-modal.js') }}"></script>
    <script>

        (function($){
            $(document).on('click', '.fn-tl-del-media', function(e){

                var _confirm = confirm('{{ trans('netframe.confirmDelMedia')}}');

                if (!_confirm) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
                else{

                    e.preventDefault();
                    el = $(this);


                    $.post('{{ url()->to('/') }}' + laroute.route('user_delete_media',{id: el.data('media')})).success(function (data) {
                        if(data.result == 'delete'){
                            el.closest('li').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }
                    });
                }
            });
        })(jQuery);


        $(window).load(function() {
            $('.flexslider').flexslider({
                animation: 'slide',
                animationLoop: false,
                itemWidth: 115
            });

            PlayerModal({
                $modal: $('#sliderPlayer'),
                $inModal: true,
                $backwardButton: $('#backwardButton'),
                $forwardButton: $('#forwardButton'),
                $mediaTitle: $('#playerMediaTitle'),
                $playerWrapper: $('#mediaPlayerWrapper'),
                $media: $('.playlistItem'),
                $mediaTypes: 'all',
                baseUrl: "{{ url()->to('/') }}"
            });
        });

        function unstarAll(){
            $(".favoriteStar").each(function(){
                $(this).removeClass('starred');
            });
        }
    </script>
@stop
