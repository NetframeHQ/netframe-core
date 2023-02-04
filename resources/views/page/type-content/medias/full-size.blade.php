@if(isset($medias))
    @foreach($medias as $media)
        @if ($media->platform !== 'local' && $media->active == 1)
            @if($gdpr_agrement)
                <div class="tl-video-feed-contain text-center">
                    <div class="tl-video-feed">
                    @if($media->platform == 'youtube')
                        <iframe id="ytplayer" type="text/html" src="https://www.youtube.com/embed/{{ $media->file_name }}" width="100%" height="280px" frameborder="0" allowfullscreen></iframe>
                    @elseif($media->platform == 'dailymotion')
                        <iframe src="https://www.dailymotion.com/embed/video/{{ $media->file_name }}?api=true" width="100%" height="280px" frameborder="0"></iframe>
                    @elseif($media->platform == 'vimeo')
                        <iframe src="//player.vimeo.com/video/{{ $media->file_name }}" width="100%" height="280px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    @elseif($media->platform == 'soundcloud')
                        <iframe width="100%" height="280px" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{ $media->file_name }}&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>
                    @endif
                    </div>
                </div>
            @else
                <div class="tl-video-feed-contain text-center">
                    {{ trans('netframe.privacyContent') }}
                </div>
            @endif
        @elseif ($media->type == \Netframe\Media\Model\Media::TYPE_VIDEO && $media->active == 1)
            <div class="tl-video-feed-contain text-center">
                <div class="tl-video-feed">
                    <video class="video-js vjs-default-skin" controls preload="auto" width="100%" height="300" data-setup='{ "poster": "{{ url()->route('media_download', array('id' => $media->id)) }}?feed=1" }'>
                        <source src="{{ url()->route('media_download', array('id' => $media->id)) }}" type="{{ $media->mime_type }}" />
                    </video>
                </div>
            </div>
        @elseif ($media->type == \Netframe\Media\Model\Media::TYPE_AUDIO && $media->active == 1)
            <div class="tl-audio-feed text-center">
                <audio class="audio-js" src="{{ url()->route('media_download', array('id' => $media->id)) }}" preload="auto"  controls ></audio>
            </div>
        @elseif ($media->type == \Netframe\Media\Model\Media::TYPE_IMAGE && $media->active == 1)
            @if(count($medias) == 1)
            <ul class="list-gallery">
                <li class="gallery-item">
                    <a class="viewMedia text-center"
                        data-media-name="{{ $media->name }}"
                        data-media-id="{{ $media->id }}"
                        data-media-type="{{ $media->type }}"
                        data-media-platform="{{ $media->platform }}"
                        data-media-mime-type="{{ $media->mime_type }}"

                        @if ($media->platform !== 'local' )
                            data-media-file-name="{{ $media->file_name }}"
                        @endif

                        style="background-image:url({{ url()->route('media_download', array('id' => $media->id, 'feed' => (!isset($fullSize) ? 1 : 0 ))) }})"
                        >
                        <div class="nf-thumbnail" style="background-image:url({{ url()->route('media_download', array('id' => $media->id, 'feed' => (!isset($fullSize) ? 1 : 0 ))) }})"></div>
                    </a>
                </li>
            </ul>
            @elseif(!isset($fullSize))
                <div class="panel-document">
                    <div class="panel-document-head">
                        <a href="{{ url()->route('media_download', array('id' => $media->id)) }}" target="_blank" class="viewMedia nf-invisiblink"
                            data-media-name="{{ $media->name }}"
                            data-media-id="{{ $media->id }}"
                            data-media-type="{{ $media->type }}"
                            data-media-platform="{{ $media->platform }}"
                            data-media-mime-type="{{ $media->mime_type }}"

                            @if ($media->platform !== 'local')
                                data-media-file-name="{{ $media->file_name }}"
                            @endif
                        ></a>

                        <div class="panel-document-icon">
                            <div class="panel-document-preview" style="background-image: url({{ url()->route('media_download', ['id' => $media->id, 'thumb' => 1]) }})"></div>
                        </div>
                        <div class="panel-document-info">
                            <h3 class="panel-document-title">{{ $media->name }}</h3>
                            <p class="panel-document-subtitle">{{ $media->formatSizeUnits() }}</p>
                        </div>
                        <!-- <div class="nf-btn">
                            <span class="btn-txt">{{ trans('page.openDocument') }}</span>
                        </div> -->

                        
                    </div>
                </div>
            @endif

        @elseif (($media->type == \Netframe\Media\Model\Media::TYPE_ARCHIVE || $media->isDocument())  && $media->active == 1)
            <div class="panel-document ">

                <div class="panel-document-head">
                    @php
                      /* Permet de dwl et éditer si Office est présent sur l'instance */
                      if($media->isDocument() && $activeOffice && "application/pdf" !== $media->mime_type) {
                        $link = url()->route('office.document', array('documentId' => $media->id));
                        $download = false;

                      /* ouvre les docs office avec le lecteur pdf */
                      } elseif ($media->isDocument() && !$activeOffice && $media->feed_path != null) {
                        $link = url()->route('media.pdf.viewer').'?file='.urlencode(URL::route('media_download', ['id' => $media->id, 'feed' => true]));
                        $download = false;

                      /* Ouvre un PDF avec la visionneuse */
                      } elseif ("application/pdf" === $media->mime_type) {
                        $link = url()->route('media.pdf.viewer').'?file='.URL::route('media_download', ['id' => $media->id]);
                        $download = false;

                      /* Télécharge le document */
                      } else {
                        $link = url()->route('media_download', array('id' => $media->id));
                        $download = true;
                      }

                      $rightsMedia = App\Http\Controllers\BaseController::hasRightsProfile($media->folder(), 5);
                    @endphp
                    <a href="{{ $link }}" class="nf-invisiblink" target="_blank" @if($download) download @endif></a>

                    <div class="panel-document-icon" data-type-mime="{{ $media->mime_type }}">
                        @if($media->thumb_path!=null or $media->thumb_path!="")
                        <div class="panel-document-preview" style="background-image: url({{ url()->route('media_download', ['id' => $media->id, 'thumb' => 1]) }}")"></div>
                        @endif
                    </div>
                    <div class="panel-document-info">
                        <h3 class="panel-document-title">{{ $media->name }}</h3>
                        <p class="panel-document-subtitle">{{ $media->formatSizeUnits() }}</p>
                    </div>
                    @if($media->mainProfile() != null)
                        @include('media.partials.menu-actions', [
                            'rights' => $rightsMedia,
                            'profileType' => (isset($post)) ? $post->author->getType() : $media->mainProfile()->getType(),
                            'profileId' => (isset($post)) ? $post->author->id : $media->mainProfile()->id,
                            'openLocation' => true
                        ])
                    @endif

                    <!-- VIEW BUTTON -->
                    @php
                        //$mediaViews = $media->views()->groupBy('views.users_id')->with('user')->limit(5)->get();
                        $viewsCount = $media->views('trueViews')->count();
                    @endphp

                    @if($viewsCount > 0)
                        <div class="nf-post-actions nf-action-view">
                            <div href="{{ url()->route('media.details', ['id' => $media->id]) }}" class="nf-btn" data-toggle="modal" data-target="#modal-ajax">
                                <span class="btn-img svgicon">
                                    @include('macros.svg-icons.view')
                                </span>
                                <span class="btn-txt btn-digit">{{ $viewsCount }}</span>
                                <span class="btn-txt">{{ trans_choice('netframe.views', $viewsCount) }}</span>
                            </div>
                            {{--  Remove tooltip for media vienw and gain 2 queries per media
                            <div class="nf-tooltip">
                                <div class="tooltip-list">
                                    <ul>
                                    @foreach($mediaViews as $view)
                                        <li>
                                            <a href="{{$view->user->getUrl()}}">{{$view->user->getNameDisplay()}}</a>
                                        </li>
                                        @endforeach
                                        @if($viewsCount>5)
                                        <li>
                                            <a href="{{ route('post.viewers', ['elementType'=>str_replace('App\\', '', get_class($media)),'elementId'=>$media->id]) }}" data-toggle="modal" data-target="#modal-ajax-thin">
                                                …
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    @if($viewsCount <= 1)
                                        <p class="tooltip-txt">{{trans('netframe.viewedThisPostSingular')}}</p>
                                    @else
                                        <a href="{{ route('post.viewers', ['elementType'=>str_replace('App\\', '', get_class($media)),'elementId'=>$media->id]) }}" class="tooltip-txt" data-toggle="modal" data-target="#modal-ajax-thin">
                                            {{trans('netframe.viewedThisPost')}}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            --}}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
@endif