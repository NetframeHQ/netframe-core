<div class="exhibit clearfix">
    <div class="text-center">
        <!-- extract five first playlist items -->
        <a href="{{ url()->route('playlist_show', ['id' => $post->post->id]) }}">
            @foreach($post->post->itemsNf as $playlistItem)
                <?php
                if($playlistItem->medias_id != null && $playlistItem->medias_id != 0) {
                    $media = \App\Media::find($playlistItem->medias_id);
                } else {
                    $media = $playlistItem->profile->getFavoriteOrLastMedia();
                }
                ?>

                @if($media !== null)
                    {!! HTML::thumbnail($media, 100, 100, array('class' => 'img-thumbnail'),asset('assets/img/no-media.jpg')) !!}
                @else
                    {!! HTML::thumbnail($playlistItem->profile->profileImage, 100, 100, array('class' => 'img-thumbnail'),asset('assets/img/avatar/'.$playlistItem->profile->getType().'.jpg')) !!}
                @endif

            @endforeach
        </a>
    </div>

    <div class="padding-5">
        <h3>{{ $post->post->name }}</h3>
        <p>{{ $post->post->description }}</p>
    </div>
</div>