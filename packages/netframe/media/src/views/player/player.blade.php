@if ($media->platform === 'local')

	@if ($media->type === \Netframe\Media\Model\Media::TYPE_VIDEO)
    	<video class="video-js vjs-default-skin" controls preload="auto" width="{{ $attributes->width }}" height="{{ $attributes->height }}">
    		<source src="{{ url()->route('media_download', array('id' => $media->id)) }}" type="{{ $media->mime_type }}" />
    	</video>

    @elseif ($media->type === \Netframe\Media\Model\Media::TYPE_AUDIO)
    	<audio src="{{ url()->route('media_download', array('id' => $media->id)) }}" preload="auto"></audio>

    @elseif ($media->type === \Netframe\Media\Model\Media::TYPE_IMAGE)
        @if ($media->thumb_path)
            <img src="{{ url()->route('media_download', array('id' => $media->id, 'thumb' => 1)) }}"
            style="width: {{ $attributes->width }}; height: {{ $attributes->height }};"/>

        @else
            <img src="{{ url()->route('media_download', array('id' => $media->id)) }}"
            style="width: {{ $attributes->width }}; height: {{ $attributes->height }};"/>
        @endif

    @elseif ($media->type === \Netframe\Media\Model\Media::TYPE_ARCHIVE || $media->type === \Netframe\Media\Model\Media::TYPE_DOCUMENT)
    	<a class="btn btn-default" href="{{ url()->route('media_download', array('id' => $media->id)) }}">
    	    <span class="glyphicon glyphicon-download"></span> {{ trans('media::messages.download') }}
    	</a>
    @endif

@else
	@foreach ($importers as $importer)
		@if ($media->platform === $importer->getPlatform())
			@include('media::player.importers.' . $importer->getPlatform(), array('media' => $media, 'attributes' => $attributes))
		@endif
	@endforeach
@endif