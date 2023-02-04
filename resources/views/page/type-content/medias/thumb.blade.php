@foreach($medias as $media)
    <li class="gallery-item">
        @if ($media->encoded == 1 && $media->active == 1)
            @if ($media->type == \Netframe\Media\Model\Media::TYPE_DOCUMENT || $media->type == \Netframe\Media\Model\Media::TYPE_ARCHIVE)
                <a class="btn btn-default" href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank">
                    <span class="glyphicon glyphicon-download"></span> {{ $media->name }}
                </a>
            @else
                <a class="viewMedia"
                    data-media-name="{{ $media->name }}"
                    data-media-id="{{ $media->id }}"
                    data-media-type="{{ $media->type }}"
                    data-media-platform="{{ $media->platform }}"
                    data-media-mime-type="{{ $media->mime_type }}"

                    @if ($media->platform !== 'local')
                        data-media-file-name="{{ $media->file_name }}"
                    @endif
                    >
                    {!! \HTML::thumbnail($media, 100, 100, array('class' => 'img-thumbnail')) !!}
                </a>
            @endif
        @endif
    </li>
@endforeach