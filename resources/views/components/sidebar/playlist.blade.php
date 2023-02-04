<section id="mini-mosaic-playlists" class="block-widget">
        @if(isset($routeMore))
    <a href="{{ $routeMore }}" class="btn-xs btn-default float-right modal-hidden" data-toggle="modal" data-target="#modal-ajax">{{ trans('netframe.viewAll') }}</a>
        @endif
    <h2 class="widget-title">{{ trans('widgets.'.$prefixTranslate.'playlists') }}</h2>
    @foreach($playlists as $playlist)
        @if( count($playlist->itemsNf) > 0)
            <div class="clearfix">
                <p>{{ $playlist->name }}</p>
                <!-- extract five first playlist items -->
                <ul class="block-mosaic">
                    @foreach($playlist->itemsNf->take(4) as $playlistItem)
                        <?php
                        if($playlistItem->medias_id != null && $playlistItem->medias_id != 0){
                            $media = \App\Media::find($playlistItem->medias_id);
                        }
                        else{
                            $media = $playlistItem->profile->getFavoriteOrLastMedia();
                        }
                        ?>
                    <li class="mosaic-item col-md-3 col-xs-3">
                        <a href="{{ url()->route('playlist_show', ['id' => $playlist->id]) }}">
                        @if($media !== null)
                            {!! HTML::thumbnail($media, '', '', ['class' => 'img-fluid'], asset('assets/img/no-media.jpg')) !!}
                        @else
                            {!! HTML::thumbnail($playlistItem->profile->profileImage, '', '', ['class' => 'img-fluid'], asset('assets/img/avatar/'.$playlistItem->profile->getType().'.jpg')) !!}
                        @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</section>
