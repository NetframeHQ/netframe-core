@if (count($medias) > 0)
<section id="widget-profile-medias" class="block-widget">
    @if(isset($routeMore))
    <a href="{{ $routeMore }}" class="btn-xs btn-default float-right modal-hidden" data-toggle="modal" data-target="#modal-ajax">{{ trans('netframe.viewAll') }}</a>
    @endif
    <h2 class="widget-title">{{ trans('widgets.newMedias') }}</h2>
        <ul class="block-mosaic row">
        @foreach($medias as $media)
            @if ($media->encoded && $media->active == 1)
                <li class="mosaic-item col-md-3 col-xs-3">
                    @if (!$media->isTypeDisplay())
                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" >
                    @else
                        <a href="" class="viewMedia"
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
                        {!! HTML::thumbnail($media, '', '', array('class' => 'img-fluid')) !!}
                        </a>

                    <div class="mosaic-footer">
                        <p class="thumb-mosaic-category">
                            <a href="{{ $media->author->first()->getUrl() }}">
                                {{ $media->author->first()->getNameDisplay() }}
                            </a>
                        </p>
                    </div>
                </li>
            @endif
        @endforeach
        </ul>
</section>
@endif

@if(!isset($routeMore))
    <script>
    (function($){
        //for modal media view
        var $modal = $('#viewMediaModal');

        new PlayMediaModal({
            $modal: $modal,
            $modalTitle: $modal.find('.modal-title'),
            $modalContent: $modal.find('.modal-body'),
            $media: $('.viewMedia'),
            baseUrl: baseUrl
        });
    })(jQuery);
    </script>
@endif
