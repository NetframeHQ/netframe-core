    @if(count($toutNetframe['newProfiles']) > 0)
        <section class="block-widget">
            <h2 class="widget-title">{{ trans('widgets.newProfiles') }}</h2>
            <ul class="list-inline block-mosaic mg-0">
            @foreach($toutNetframe['newProfiles'] as $profile)
                <li class="mosaic-item col-xs-3 list-inline-item">
                    <a href="{{ $profile->getUrl() }}">
                        <div class="mosaic-content">
                        @if(isset($profile->profile_media_id))
                            <img src="{{ url()->route('media_download', ['id' => $profile->profile_media_id, 'thumb' => 1]) }}"
                                class="img-fluid"
                            />
                        @else
                            <img src="{{ asset('assets/img/avatar/'.$profile->getType().'.jpg') }}" alt="no-image" class="img-fluid" />
                        @endif
                        </div>
                        <div class="mosaic-footer">
                            <p class="thumb-mosaic-category">
                                {{ $profile->getNameDisplay() }}
                            </p>
                        </div>
                    </a>
                </li>
            @endforeach
            </ul>
        </section>
    @endif

    <section class="block-widget">
        <h2 class="widget-title">{{ trans('widgets.newMedias') }}</h2>
        <ul class="list-inline block-mosaic mg-0">
            @foreach($toutNetframe['newMedias'] as $media)
                @if ($media->encoded && $media->active == 1)
                    <li class="mosaic-item col-xs-3 list-inline-item">
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

@include('components.sidebar.events', ['events' => $toutNetframe['newEvents'] ])
@include('components.sidebar.last-news', ['lastNews' => $toutNetframe['lastNews'] ])
@include('components.sidebar.last-actions', ['lastActions' => $toutNetframe['lastActions'] ])