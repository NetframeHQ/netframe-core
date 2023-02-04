@if (count($lastNews) > 0)
    <section id="widget-profile-medias" class="block-widget event">
        <h2 class="widget-title">{{ trans('widgets.lastNews') }}</h2>

        <ul class="list-unstyled">
        @foreach($lastNews as $post)
            <li class="media">
                <a href="{{ $post->post->author->getUrl() }}/{{ $post->id }}">
                    <div class="media-left">
                        {{ HTML::thumbnail($post->post->author->profileImage, '60', '60', ['class' => 'img-fluid profile-image'],
                            asset('/assets/img/avatar/'.$post->post->author->getType().'.jpg'))
                        }}
                    </div>
                    <div class="media-body">
                        {{--*/ $postMedia = $post->post->medias()->first() /*--}}
                        @if($post->post->content == null && $postMedia != null)
                            @if($postMedia->platform == 'local')
                                {{ trans('page.newMedia.local.'.$postMedia->type) }}
                            @else
                                {{ trans('page.newMedia.platform.'.$postMedia->platform) }}
                            @endif
                        @else
                            <h3>{{ \App\Helpers\StringHelper::formatMetaText($post->post->content, 100) }}</h3>
                        @endif
                    </div>
                    @if($postMedia != null)
                        <div class="media-right">
                            {{ HTML::thumbnail($postMedia, '60', '60', ['class' => 'img-fluid profile-image'], '')}}
                        </div>
                    @endif
                </a>
            </li>
        @endforeach
        </ul>
    </section>
@endif
