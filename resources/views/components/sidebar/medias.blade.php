<section id="widget-medias" class="block-widget">
    <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}" class="btn-xs btn-default float-right modal-hidden">{{ trans('netframe.viewAll') }}</a>
    <h2 class="widget-title">{{ trans('widgets.documents') }}</h2>
    @if($starMedias->count() > 0)
        <ul class="block-mosaic row">
            @foreach($starMedias as $media)
                <li class="col-md-6 col-xs-6 media-line">
                    @if (!$media->isTypeDisplay())
                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="media">
                    @else
                        <a href="" class="viewMedia media"
                            data-media-name="{{ $media->name }}"
                            data-media-id="{{ $media->id }}"
                            data-media-type="{{ $media->type }}"
                            data-media-platform="{{ $media->platform }}"
                            data-media-mime-type="{{ $media->mime_type }}"

                            @if ($media->platform !== 'local')
                                data-media-file-name="{{ $media->file_name }}"
                            @endif
                        >
                    @endif
                        <div class="preview-media">
                            {!! HTML::thumbnail($media, '20', '20', array('class' => 'img-fluid profile-image')) !!}
                        </div>
                        <span class="name">
                            <span>{{ $media->name }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @elseif($rights && $rights <=4)
        <p class="text-center mg-bottom">
            <a href="{{ url()->route('medias_explorer', ['profileType' => $profile->getType(), 'profileId' => $profile->id]) }}" class="btn btn-border-default">
                {{ trans('xplorer.sidebar.addMedias') }}
            </a>
        </p>
    @endif
</section>