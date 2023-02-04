{{--
<!-- @Julien, on ne devrait pas rajouter un "@if il y a des medias".  S'il n'y a pas de médias on ne devrait voir aucun html et j'ai un bug ou des panel gallery vide se retrouvent dans des panbel exhibit et casse les styles du bloc du coup  -->
--}}

<div class="panel-gallery">
    <ul class="list-gallery">
        <?php
            if (isset($post->post)) {
                $medias = $post->post->medias;
            } else {
                $medias = $post->medias;
            }
            $i = count($medias);
            $j = 0;
            $nbImg = $i - 4;
        ?>
        @foreach($medias as $media)
            @if($j < 4)
                <li class="gallery-item">
                    <a class="viewMedia text-center"
                    data-media-name="{{ $media->name }}"
                    data-media-id="{{ $media->id }}"
                    data-media-type="{{ $media->type }}"
                    data-media-platform="{{ $media->platform }}"
                    data-media-mime-type="{{ $media->mime_type }}"
                    @if ($media->platform !== 'local')data-media-file-name="{{ $media->file_name }}"@endif
                    @if ($media->type == \Netframe\Media\Model\Media::TYPE_IMAGE)style="background-image:url({{ url()->route('media_download', array('id' => $media->id)).'?v='.$media->updated_at->format('U') }})"@endif
                    >
                        @if ($media->type == \Netframe\Media\Model\Media::TYPE_IMAGE)
                            {!! \HTML::thumbnail($media, '', '', []) !!}
                        @elseif($media->type == \Netframe\Media\Model\Media::TYPE_AUDIO)
                            {!! \HTML::thumbnail($media, '', '', []) !!}
                        @elseif($media->type == \Netframe\Media\Model\Media::TYPE_VIDEO)
                            {!! \HTML::thumbnail($media, '', '', []) !!}
                        @else
                            <!-- TODO : BUG HERE — div inside p -->
                            <p>
                                {!! \HTML::thumbnail($media, '400', '400', []) !!}
                                <br>
                                <span class="filename">
                                    {{ $media->name }}
                                </span>
                            </p>
                        @endif

                        @if($i > 4 && $j == 3)
                            <span class="overlay"><p>+<?php echo $nbImg; ?></p></span>
                        @else
                            @if($media->mainProfile() != null)
                                @include('media.partials.menu-actions', [
                                    'rights' => App\Http\Controllers\BaseController::hasRightsProfile($media->folder(), 5),
                                    'profileType' => $media->mainProfile()->getType(),
                                    'profileId' => $media->mainProfile()->id,
                                    'openLocation' => true
                                ])
                            @endif
                        @endif
                    </a>
                </li>
            @endif
            <?php $j++; ?>
        @endforeach
    </ul>
</div>